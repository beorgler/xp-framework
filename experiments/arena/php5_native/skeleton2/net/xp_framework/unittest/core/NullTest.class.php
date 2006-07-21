<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.profiling.unittest.TestCase');

  /**
   * Tests the "NULL-safe" xp::null. Requires the overload extension to
   * be enabled.
   *
   * @purpose  Testcase
   */
  class NullTest extends TestCase {

    /**
     * Setup method. Ensures overload extension is enabled.
     *
     * @access  public
     */
    public function setUp() {
      if (!extension_loaded('overload')) {
        throw(new PrerequisitesNotMetError(
          'Overload extension not enabled', 
          $cause= NULL
        ));
      }
    }
    
    /**
     * Tests the is() core function will recognize xp::null as null
     *
     * @access  public
     */
    #[@test]
    public function isNull() {
      $this->assertTrue(is(NULL, xp::null()));
    }

    /**
     * Tests the is() core function will not recognize xp::null as an object
     *
     * @access  public
     */
    #[@test]
    public function isNotAnObject() {
      $this->assertFalse(is('lang.Object', xp::null()));
    }

    /**
     * Tests xp::null evaluates to FALSE so it can be used in constructs
     * such as:
     * <code>
     *   function &constructorOf(&$class) {
     *     if (!$class->hasConstructor()) return xp::null();
     *     return $class->getConstructor();
     *   }
     *   
     *   if (!($c= &constructorOf($class))) { ... }
     * </code>
     *
     * @access  public
     */
    #[@test]
    public function isFalse() {
      $this->assertTrue(!xp::null());
    }

    /**
     * Tests the xp::typeOf() function's return value for xp::nulls
     *
     * @access  public
     */
    #[@test]
    public function typeOf() {
      $this->assertEquals('<null>', xp::typeOf(xp::null()));
    }
    
    /**
     * Tests the xp::stringOf() function's return value for xp::nulls
     *
     * @access  public
     */
    #[@test]
    public function stringOf() {
      $this->assertEquals('<null>', xp::stringOf(xp::null()));
    }
    
    /**
     * Tests creating new instances of xp::null will fail. The correct
     * way to retrieve an xp::null is to call xp:null()
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalAccessException')]
    public function newInstance() {
      new null();
    }

    /**
     * Tests cloning xp::null will result in a NPE
     *
     * @access  public
     */
    #[@test, @expect('lang.NullPointerException')]
    public function clone() {
      clone(xp::null());
    }

    /**
     * Tests member invocation on xp::null will result in a NPE
     *
     * @access  public
     */
    #[@test, @expect('lang.NullPointerException')]
    public function methodInvocation() {
      $null= &xp::null();
      $null->method();
    }

    /**
     * Tests member read access on xp::null will result in a NPE
     *
     * @access  public
     */
    #[@test, @expect('lang.NullPointerException')]
    public function memberReadAccess() {
      $null= &xp::null();
      $i= $null->member;
    }
    
    /**
     * Tests member write access on xp::null will result in a NPE
     *
     * @access  public
     */
    #[@test, @expect('lang.NullPointerException')]
    public function memberWriteccess() {
      $null= &xp::null();
      $null->member= $i;
    }
  }
?>
