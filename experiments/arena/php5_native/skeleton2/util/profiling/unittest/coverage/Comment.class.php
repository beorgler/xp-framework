<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Represents a comment
   *
   * @purpose  Helper class
   */
  class Comment extends Object implements Fragment {
    public
      $text   = '',
      $start  = 0,
      $end    = 0;
      
    /**
     * Constructor
     *
     * @access  public
     * @param   string text
     * @param   int start the first line
     * @param   int end the last line
     */
    public function __construct($text, $start, $end) {
      $this->text= $text;
      $this->start= $start;
      $this->end= $end;
    }
    
    /**
     * Checks if a specified object is equal to this object.
     *
     * @access  public
     * @param   &lang.Object expr
     * @return  bool
     */
    public function equals(&$expr) {
      return (
        is('Comment', $expr) && 
        $this->text == $expr->text &&
        $this->start == $expr->start &&
        $this->end == $expr->end
      );
    }

    /**
     * Creates a string representation of this object
     *
     * @access  public
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'@({'.$this->text.'} at lines '.$this->start.' - '.$this->end.')';
    }

  } 
?>
