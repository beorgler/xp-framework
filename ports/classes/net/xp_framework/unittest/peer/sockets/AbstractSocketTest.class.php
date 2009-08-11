<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'peer.Socket',
    'lang.Runtime'
  );

  /**
   * TestCase
   *
   * @see      xp://peer.Socket
   */
  abstract class AbstractSocketTest extends TestCase {
    const SERVER_ADDR= '127.0.0.1';
    const SERVER_PORT= 2122;
    
    protected static $serverProcess = NULL;
    protected $fixture= NULL;
    
    /**
     * Creates a new client socket
     *
     * @param   string addr
     * @param   int port
     * @return  peer.Socket
     */
    protected abstract function newSocket($addr, $port);

    /**
     * Setup this test case
     *
     */
    public function setUp() {
      $this->fixture= $this->newSocket(self::SERVER_ADDR, self::SERVER_PORT);
    }

    /**
     * Tears down this test case
     *
     */
    public function tearDown() {
      $this->fixture->isConnected() && $this->fixture->close();
    }

    /**
     * Starts server in background
     *
     */
    #[@beforeClass]
    public static function startServer() {

      // Start server process
      with ($rt= Runtime::getInstance()); {
        self::$serverProcess= $rt->getExecutable()->newInstance(array_merge(
          $rt->startupOptions()->asArguments(),
          array($rt->bootstrapScript()),
          array('net.xp_framework.unittest.peer.sockets.TestingServer', self::SERVER_ADDR, self::SERVER_PORT)
        ));
      }
      self::$serverProcess->in->close();

      // Check if startup succeeded
      $status= self::$serverProcess->out->readLine();
      if (!strlen($status) || '+' != $status{0}) {
        try {
          self::shutdownServer();
        } catch (IllegalStateException $e) {
          $status.= $e->getMessage();
        }
        throw new PrerequisitesNotMetError('Cannot start server: '.$status, NULL);
      }
    }

    /**
     * Shut down FTP server
     *
     */
    #[@afterClass]
    public static function shutdownServer() {

      // Tell the server to shut down
      try {
        $c= new Socket(self::SERVER_ADDR, self::SERVER_PORT);
        $c->connect();
        $c->write("HALT\n");
        $c->close();
      } catch (Throwable $ignored) {
        // Fall through, below should terminate the process anyway
      }

      $status= self::$serverProcess->out->readLine();
      if (!strlen($status) || '+' != $status{0}) {
        while ($l= self::$serverProcess->out->readLine()) {
          $status.= $l;
        }
        while ($l= self::$serverProcess->err->readLine()) {
          $status.= $l;
        }
        self::$serverProcess->close();
        throw new IllegalStateException($status);
      }
      self::$serverProcess->close();
    }
  
    /**
     * Test
     *
     */
    #[@test]
    public function initiallyNotConnected() {
      $this->assertFalse($this->fixture->isConnected());
    }

    /**
     * Test connecting
     *
     */
    #[@test]
    public function connect() {
      $this->assertTrue($this->fixture->connect());
      $this->assertTrue($this->fixture->isConnected());
    }

    /**
     * Test connecting
     *
     */
    #[@test, @expect('peer.ConnectException')]
    public function connectInvalidPort() {
      $this->newSocket(self::SERVER_ADDR, -1)->connect(0.1);
    }

    /**
     * Test connecting
     *
     */
    #[@test, @expect('peer.ConnectException')]
    public function connectInvalidHost() {
      $this->newSocket(NULL, self::SERVER_PORT)->connect(0.1);
    }

    /**
     * Test connecting
     *
     */
    #[@test, @expect('peer.ConnectException')]
    public function connectUnConnected() {
      $this->newSocket(self::SERVER_ADDR, self::SERVER_PORT+ 1)->connect(0.1);
    }

    /**
     * Test closing
     *
     */
    #[@test]
    public function closing() {
      $this->assertTrue($this->fixture->connect());
      $this->assertTrue($this->fixture->close());
      $this->assertFalse($this->fixture->isConnected());
    }

    /**
     * Test closing
     *
     */
    #[@test]
    public function closingNotConnected() {
      $this->assertFalse($this->fixture->close());
    }

    /**
     * Test writing data
     *
     */
    #[@test]
    public function write() {
      $this->fixture->connect();
      $this->assertEquals(10, $this->fixture->write("ECHO data\n"));
    }

    /**
     * Test writing data
     *
     */
    #[@test, @expect('peer.SocketException')]
    public function writeUnConnected() {
      $this->fixture->write('Anything');
    }

    /**
     * Test writing data after EOF
     *
     */
    #[@test, @ignore('Writes still succeed after close - no idea why...')]
    public function writeAfterEof() {
      $this->fixture->connect();
      $this->fixture->write("CLOS\n");
      try {
        $this->fixture->write('Anything');
        $this->fail('No exception raised', NULL, 'peer.SocketException');
      } catch (SocketException $expected) {
        // OK
      }
    }

    /**
     * Test reading data w/ readLine()
     *
     */
    #[@test]
    public function readLine() {
      $this->fixture->connect();
      $this->fixture->write("ECHO data\n");
      $this->assertEquals("+ECHO data", $this->fixture->readLine());
    }

    /**
     * Test readLine()
     *
     */
    #[@test, @expect('peer.SocketException')]
    public function readLineUnConnected() {
      $this->fixture->readLine();
    }

    /**
     * Test reading after eof w/ readLine()
     *
     */
    #[@test]
    public function readLineOnEof() {
      $this->fixture->connect();
      $this->fixture->write("CLOS\n");
      $this->assertNull($this->fixture->readLine());
      $this->assertTrue($this->fixture->eof(), '<EOF>');
    }

    /**
     * Test reading multiple lines separated by \n
     *
     */
    #[@test]
    public function readLinesWithLineFeed() {
      $this->fixture->connect();
      $this->fixture->write("LINE 5 %0A\n");
      for ($i= 0; $i < 5; $i++) {
        $this->assertEquals('+LINE '.$i, $this->fixture->readLine(), 'Line #'.$i);
      }
      $this->assertEquals('+LINE .', $this->fixture->readLine());
    }

    /**
     * Test reading multiple lines separated by \r
     *
     */
    #[@test, @ignore('readLine() only works for \n at the moment')]
    public function readLinesWithCarriageReturn() {
      $this->fixture->connect();
      $this->fixture->write("LINE 5 %0D\n");
      for ($i= 0; $i < 5; $i++) {
        $this->assertEquals('+LINE '.$i, $this->fixture->readLine(), 'Line #'.$i);
      }
      $this->assertEquals('+LINE .', $this->fixture->readLine());
    }

    /**
     * Test reading multiple lines separated by \r\n
     *
     */
    #[@test, @ignore('readLine() only works for \n at the moment')]
    public function readLinesWithCarriageReturnLineFeed() {
      $this->fixture->connect();
      $this->fixture->write("LINE 5 %0D%0A\n");
      for ($i= 0; $i < 5; $i++) {
        $this->assertEquals('+LINE '.$i, $this->fixture->readLine(), 'Line #'.$i);
      }
      $this->assertEquals('+LINE .', $this->fixture->readLine());
    }

    /**
     * Test reading data w/ read()
     *
     */
    #[@test]
    public function read() {
      $this->fixture->connect();
      $this->fixture->write("ECHO data\n");
      $this->assertEquals("+ECHO data\n", $this->fixture->read());
    }

    /**
     * Test read()
     *
     */
    #[@test, @expect('peer.SocketException')]
    public function readUnConnected() {
      $this->fixture->read();
    }

    /**
     * Test reading after eof w/ read()
     *
     */
    #[@test]
    public function readOnEof() {
      $this->fixture->connect();
      $this->fixture->write("CLOS\n");
      $this->assertNull($this->fixture->read());
      $this->assertTrue($this->fixture->eof(), '<EOF>');
    }

    /**
     * Test reading data w/ readBinary()
     *
     */
    #[@test]
    public function readBinary() {
      $this->fixture->connect();
      $this->fixture->write("ECHO data\n");
      $this->assertEquals("+ECHO data\n", $this->fixture->read());
    }

    /**
     * Test readBinary()
     *
     */
    #[@test, @expect('peer.SocketException')]
    public function readBinaryUnConnected() {
      $this->fixture->readBinary();
    }

    /**
     * Test reading after eof w/ readBinary()
     *
     */
    #[@test]
    public function readBinaryOnEof() {
      $this->fixture->connect();
      $this->fixture->write("CLOS\n");
      $this->assertEquals('', $this->fixture->readBinary());
      $this->assertTrue($this->fixture->eof(), '<EOF>');
    }

    /**
     * Test canRead()
     *
     */
    #[@test]
    public function canRead() {
      $this->fixture->connect();
      $this->assertFalse($this->fixture->canRead(0.1));
    }

    /**
     * Test canRead()
     *
     */
    #[@test, @expect('peer.SocketException')]
    public function canReadUnConnected() {
      $this->fixture->canRead(0.1);
    }

    /**
     * Test canRead()
     *
     */
    #[@test]
    public function canReadWithData() {
      $this->fixture->connect();
      $this->fixture->write("ECHO data\n");
      $this->assertTrue($this->fixture->canRead(0.1));
    }

    /**
     * Test getHandle()
     *
     */
    #[@test]
    public function getHandle() {
      $this->fixture->connect();
      $this->assertTrue(is_resource($this->fixture->getHandle()));
    }

    /**
     * Test getHandle()
     *
     */
    #[@test]
    public function getHandleAfterClose() {
      $this->fixture->connect();
      $this->fixture->close();
      $this->assertNull($this->fixture->getHandle());
    }

    /**
     * Test getHandle()
     *
     */
    #[@test]
    public function getHandleUnConnected() {
      $this->assertNull($this->fixture->getHandle());
    }

    /**
     * Test setTimeout()
     *
     */
    #[@test, @expect('peer.SocketTimeoutException')]
    public function readTimeout() {
      $this->fixture->connect();
      $this->fixture->setTimeout(0.1);
      $this->fixture->read();
    }

    /**
     * Test setTimeout()
     *
     */
    #[@test, @expect('peer.SocketTimeoutException')]
    public function readBinaryTimeout() {
      $this->fixture->connect();
      $this->fixture->setTimeout(0.1);
      $this->fixture->readBinary();
    }
    /**
     * Test setTimeout()
     *
     */
    #[@test, @expect('peer.SocketTimeoutException')]
    public function readLineTimeout() {
      $this->fixture->connect();
      $this->fixture->setTimeout(0.1);
      $this->fixture->readLine();
    }
  }
?>