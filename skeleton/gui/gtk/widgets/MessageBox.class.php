<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('gui.gtk.GtkGladeDialogWindow');

  // Style: Buttons
  define('MB_OK',               0x0001);
  define('MB_CANCEL',           0x0002);
  define('MB_YES',              0x0004);
  define('MB_NO',               0x0008);
  define('MB_RETRY',            0x0010);
  define('MB_IGNORE',           0x0020);
  define('MB_ABORT',            0x0030);
  
  // Style: Buttons combined
  define('MB_OKCANCEL',         MB_OK | MB_CANCEL);
  define('MB_YESNO',            MB_YES | MB_NO);
  define('MB_YESNOCANCEL',      MB_YES | MB_NO | MB_CANCEL);
  define('MB_RETRYCANCEL',      MB_RETRY | MB_CANCEL);
  
  // Style: Icons  
  define('MB_ICONHAND',         0x0040);
  define('MB_ICONQUESTION',     0x0050);
  define('MB_ICONEXCLAMATION',  0x0060);
  define('MB_ICONASTERISK',     0x0070);
  define('MB_ICONMASK',         MB_ICONHAND | MB_ICONQUESTION | MB_ICONEXCLAMATION | MB_ICONASTERISK);

  // Style: Icons aliases
  define('MB_ICONINFORMATION',  MB_ICONASTERISK);
  define('MB_ICONERROR',        MB_ICONHAND);
  define('MB_WARNING',          MB_ICONEXCLAMATION);

  /**
   * Messagebox
   *
   * <code>
   *   MessageBox::display('Hello world');
   * </code>
   *
   * @purpose Provide a widget for file dialogs
   */
  class MessageBox extends GtkGladeDialogWindow {
    var
      $message  = '',
      $caption  = '',
      $style    = 0,
      $pressed  = 0,
      $buttons  = array();
  
    /**
     * Constructor
     *
     * @access  public
     * @param   string message
     */
    function __construct($message, $caption= 'Message', $style= MB_OK, $buttons= array()) {
      $this->message= $message;
      $this->caption= $caption;
      $this->style= $style;
      $this->buttons= $buttons;
      parent::__construct(dirname(__FILE__).'/messagebox.glade', 'messagebox');
    }

    /**
     * Public static method
     *
     * @model   static
     * @access  public
     * @param   string message
     * @return  bool
     */
    function display($message, $caption= 'Message', $style= MB_OK, $buttons= array()) {
      $m= &new MessageBox($message, $caption, $style, $buttons);
      return $m->show();
    }
       
    /**
     * Initialize
     *
     * @access  public
     */
    function init() {
      $this->window->set_default_size(300, 180);
      
      // Message
      $this->label= &$this->widget('label');
      
      // Icon
      $this->icon= &$this->widget('icon');
      $loader= &new GTKPixmapLoader($this->window->window, dirname(__FILE__));
      try(); {
        $this->pixmaps= $loader->load(array(
          'information',
          'warning',
          'question',
          'error'
        ));
      } if (catch('IOException', $e)) {
        $this->cat->error($e->getStackTrace());
        
        // Well, we'll be without icons, then
      }
      
      // Action area
      $this->actionarea= $this->window->action_area;
    }
    
    /**
     * Run this
     *
     * @access  public
     */
    function run() {
      static $map= array(
        MB_ICONHAND         => 'error',
        MB_ICONQUESTION     => 'question',
        MB_ICONEXCLAMATION  => 'warning',
        MB_ICONASTERISK     => 'information'
      );

      // Set window title
      $this->window->set_title($this->caption);
      
      // Set message text
      $this->label->set_text($this->message);
      
      // Set icon
      $idx= $map[$this->style & MB_ICONMASK];
      $this->icon->set($this->pixmaps['p:'.$idx], $this->pixmaps['m:'.$idx]);
      
      // Buttons
      foreach (array('OK', 'CANCEL', 'YES', 'NO', 'RETRY', 'IGNORE', 'ABORT') as $name) {
        if ($this->style & constant('MB_'.$name)) {
		  $b= &new GtkButton(ucfirst(strtolower($name)));    // TBD: Get via gettext?
          $b->set_name($name);
		  $b->set_flags(GTK_CAN_DEFAULT);
		  $b->show();
          
          $this->connect(array($b, 'clicked'), 'onButtonClicked');
		  $this->actionarea->pack_start($b);
        }
      }
      
      parent::run();
    }
    
    /**
     * Callback for buttons
     *
     * @access  protected
     * @param   &php.GtkWidget widget
     */
    function onButtonClicked(&$widget) {
      $this->pressed= constant('MB_'.$widget->get_name());
      $this->close();
    }
    
    /**
     * Show this messagebpx
     *
     * @access  public
     * @return  bool
     */
    function show() {
      parent::show();
      return !empty($this->pressed);
    }
  }
?>
