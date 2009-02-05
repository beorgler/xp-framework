<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'io.streams.MemoryInputStream',
    'io.streams.MemoryOutputStream',
    'io.streams.Streams'
  );

  /**
   * TestCase
   *
   * @see      xp://io.streams.Streams
   * @purpose  Unittest
   */
  class StreamWrappingTest extends TestCase {
  
    /**
     * Test readableFd() method
     *
     */
    #[@test]
    public function reading() {
      $buffer= 'Hello World';
      $m= new MemoryInputStream($buffer);

      $fd= Streams::readableFd($m);
      $read= fread($fd, strlen($buffer));
      fclose($fd);
      
      $this->assertEquals($buffer, $read);
    }

    /**
     * Test feof callbacks
     *
     */
    #[@test]
    public function endOfFile() {
      $fd= Streams::readableFd(new MemoryInputStream(str_repeat('x', 10)));
      $this->assertFalse(feof($fd), 'May not be at EOF directly after opening');

      fread($fd, 5);
      $this->assertFalse(feof($fd), 'May not be at EOF after reading only half of the bytes');

      fread($fd, 5);
      $this->assertTrue(feof($fd), 'Must be at EOF after having read all of the bytes');

      fclose($fd);
    }

    /**
     * Test fstat callbacks
     *
     */
    #[@test]
    public function stat() {
      $fd= Streams::readableFd(new MemoryInputStream(str_repeat('x', 10)));
      $stat= fstat($fd);
      $this->assertEquals(10, $stat['size']);

      fread($fd, 5);
      $stat= fstat($fd);
      $this->assertEquals(10, $stat['size']);
      
      fclose($fd);
    }

    /**
     * Test ftell callbacks
     *
     */
    #[@test]
    public function tellFromReadable() {
      $fd= Streams::readableFd(new MemoryInputStream(str_repeat('x', 10)));
      $this->assertEquals(0, ftell($fd));

      fread($fd, 5);
      $this->assertEquals(5, ftell($fd));

      fread($fd, 5);
      $this->assertEquals(10, ftell($fd));
      
      fclose($fd);
    }

    /**
     * Test ftell callbacks
     *
     */
    #[@test]
    public function tellFromWriteable() {
      $fd= Streams::writeableFd(new MemoryOutputStream());
      $this->assertEquals(0, ftell($fd));

      fwrite($fd, str_repeat('x', 5));
      $this->assertEquals(5, ftell($fd));

      fwrite($fd, str_repeat('x', 5));
      $this->assertEquals(10, ftell($fd));
      
      fclose($fd);
    }

    /**
     * Test writeableFd() method
     *
     */
    #[@test]
    public function writing() {
      $buffer= 'Hello World';
      $m= new MemoryOutputStream();

      $fd= Streams::writeableFd($m);
      $written= fwrite($fd, $buffer);
      fclose($fd);
      
      $this->assertEquals(strlen($buffer), $written);
      $this->assertEquals($buffer, $m->getBytes());
    }

    /**
     * Test reading from a file descriptor opened with writeableFd()
     * results in an IOException
     *
     */
    #[@test, @expect('io.IOException')]
    public function readFromWriteableFd() {
      $fd= Streams::writeableFd(new MemoryOutputStream());
      fread($fd, 1024);
    }

    /**
     * Test reading from a file descriptor opened with writeableFd()
     * results in an IOException
     *
     */
    #[@test, @expect('io.IOException')]
    public function writeToReadableFd() {
      $fd= Streams::readableFd(new MemoryInputStream(''));
      fwrite($fd, 1024);
    }

    /**
     * Test readAll()
     *
     */
    #[@test]
    public function readAll() {
      $this->assertEquals('Hello', Streams::readAll(new MemoryInputStream('Hello')));
    }

    /**
     * Test readAll()
     *
     */
    #[@test]
    public function readAllFromEmptyStream() {
      $this->assertEquals('', Streams::readAll(new MemoryInputStream('')));
    }

    /**
     * Test readAll()
     *
     */
    #[@test, @expect('io.IOException')]
    public function readAllWithException() {
      $this->assertEquals('', Streams::readAll(newinstance('io.streams.InputStream', array(), '{
        public function read($limit= 8192) { throw new IOException("FAIL"); }
        public function available() { return 1; }
        public function close() { }
      }')));
    }
  }
?>
