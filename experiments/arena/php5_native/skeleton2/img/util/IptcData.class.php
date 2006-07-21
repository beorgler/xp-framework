<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.Date', 'img.ImagingException');

  /**
   * Reads the IPTC headers from Photoshop-files, JPEG or TIFF
   *
   * <code>
   *   uses('img.util.IptcData', 'io.File');
   *   $i= IptcData::fromFile(new File($filename));
   *   echo $i->toString();
   * </code>
   *
   * @ext      iptc
   * @purpose  Utility
   * @see     http://www.controlledvocabulary.com/pdf/IPTC_mapped_fields.pdf
   */
  class IptcData extends Object {
    public
      $title           = '',
      $urgency         = '',
      $category        = '',
      $keywords        = '',
      $dateCreated     = '',
      $author          = '',
      $authorPosition  = '',
      $city            = '',
      $state           = '',
      $country         = '',
      $headline        = '',
      $credit          = '',
      $source          = '',
      $copyrightNotice = '',
      $caption         = '',
      $writer          = '',
      $specialInstructions           = '',
      $supplementalCategories        = '',
      $originalTransmissionReference = '';

    /**
     * Read from a file
     *
     * @model   static
     * @access  public
     * @param   &io.File file
     * @return  &img.util.IptcData
     * @throws  img.ImagingException in case extracting data fails
     */
    public function &fromFile(&$file) {
      getimagesize($file->getURI(), $info);
      if (!($info['APP13'])) {
        throw(new ImagingException(
          'Cannot get IPTC information from '.$file->getURI()
        ));
      }
      $iptc= iptcparse($info['APP13']);
      
      with ($i= &new IptcData()); {
        $i->setTitle($iptc['2#005'][0]);
        $i->setUrgency($iptc['2#010'][0]);
        $i->setCategory($iptc['2#015'][0]);
        $i->setKeywords($iptc['2#025'][0]);
        $i->setDateCreated($iptc['2#055'][0]);
        $i->setAuthor($iptc['2#080'][0]);
        $i->setAuthorPosition($iptc['2#085'][0]);
        $i->setCity($iptc['2#090'][0]);
        $i->setState($iptc['2#095'][0]);
        $i->setCountry($iptc['2#101'][0]);
        $i->setHeadline($iptc['2#105'][0]);
        $i->setCredit($iptc['2#110'][0]);
        $i->setSource($iptc['2#115'][0]);
        $i->setCopyrightNotice($iptc['2#116'][0]);
        $i->setCaption($iptc['2#120'][0]);
        $i->setWriter($iptc['2#122'][0]);
        $i->setSupplementalCategories($iptc['2#20'][0]);
        $i->setSpecialInstructions($iptc['2#040'][0]);
        $i->setOriginalTransmissionReference($iptc['2#103'][0]);   
      }
      return $i;
    }

    /**
     * Set Title
     *
     * @access  public
     * @param   string title
     */
    public function setTitle($title) {
      $this->title= $title;
    }

    /**
     * Get Title
     *
     * @access  public
     * @return  string
     */
    public function getTitle() {
      return $this->title;
    }

    
    /**
     * Set Urgency
     *
     * @access  public
     * @param   string urgency
     */
    public function setUrgency($urgency) {
      $this->urgency= $urgency;
    }

    /**
     * Get Urgency
     *
     * @access  public
     * @return  string
     */
    public function getUrgency() {
      return $this->urgency;
    }

    
    /**
     * Set Category
     *
     * @access  public
     * @param   string category
     */
    public function setCategory($category) {
      $this->category= $category;
    }

    /**
     * Get Category
     *
     * @access  public
     * @return  string
     */
    public function getCategory() {
      return $this->category;
    }

    
    /**
     * Set Keywords
     *
     * @access  public
     * @param   string keywords
     */
    public function setKeywords($keywords) {
      $this->keywords= $keywords;
    }

    /**
     * Get Keywords
     *
     * @access  public
     * @return  string
     */
    public function getKeywords() {
      return $this->keywords;
    }

    
    /**
     * Set DateCreated
     *
     * @access  public
     * @param   string dateCreated
     */
    public function setDateCreated($dateCreated) {
      $this->dateCreated= $dateCreated;
    }

    /**
     * Get DateCreated
     *
     * @access  public
     * @return  string
     */
    public function getDateCreated() {
      return $this->dateCreated;
    }

    
    /**
     * Set Author
     *
     * @access  public
     * @param   string author
     */
    public function setAuthor($author) {
      $this->author= $author;
    }

    /**
     * Get Author
     *
     * @access  public
     * @return  string
     */
    public function getAuthor() {
      return $this->author;
    }

    
    /**
     * Set AuthorPosition
     *
     * @access  public
     * @param   string authorPosition
     */
    public function setAuthorPosition($authorPosition) {
      $this->authorPosition= $authorPosition;
    }

    /**
     * Get AuthorPosition
     *
     * @access  public
     * @return  string
     */
    public function getAuthorPosition() {
      return $this->authorPosition;
    }

    
    /**
     * Set City
     *
     * @access  public
     * @param   string city
     */
    public function setCity($city) {
      $this->city= $city;
    }

    /**
     * Get City
     *
     * @access  public
     * @return  string
     */
    public function getCity() {
      return $this->city;
    }

    
    /**
     * Set State
     *
     * @access  public
     * @param   string state
     */
    public function setState($state) {
      $this->state= $state;
    }

    /**
     * Get State
     *
     * @access  public
     * @return  string
     */
    public function getState() {
      return $this->state;
    }

    
    /**
     * Set Country
     *
     * @access  public
     * @param   string country
     */
    public function setCountry($country) {
      $this->country= $country;
    }

    /**
     * Get Country
     *
     * @access  public
     * @return  string
     */
    public function getCountry() {
      return $this->country;
    }

    
    /**
     * Set Headline
     *
     * @access  public
     * @param   string headline
     */
    public function setHeadline($headline) {
      $this->headline= $headline;
    }

    /**
     * Get Headline
     *
     * @access  public
     * @return  string
     */
    public function getHeadline() {
      return $this->headline;
    }

    
    /**
     * Set Credit
     *
     * @access  public
     * @param   string credit
     */
    public function setCredit($credit) {
      $this->credit= $credit;
    }

    /**
     * Get Credit
     *
     * @access  public
     * @return  string
     */
    public function getCredit() {
      return $this->credit;
    }

    
    /**
     * Set Source
     *
     * @access  public
     * @param   string source
     */
    public function setSource($source) {
      $this->source= $source;
    }

    /**
     * Get Source
     *
     * @access  public
     * @return  string
     */
    public function getSource() {
      return $this->source;
    }

    
    /**
     * Set CopyrightNotice
     *
     * @access  public
     * @param   string copyrightNotice
     */
    public function setCopyrightNotice($copyrightNotice) {
      $this->copyrightNotice= $copyrightNotice;
    }

    /**
     * Get CopyrightNotice
     *
     * @access  public
     * @return  string
     */
    public function getCopyrightNotice() {
      return $this->copyrightNotice;
    }

    
    /**
     * Set Caption
     *
     * @access  public
     * @param   string caption
     */
    public function setCaption($caption) {
      $this->caption= $caption;
    }

    /**
     * Get Caption
     *
     * @access  public
     * @return  string
     */
    public function getCaption() {
      return $this->caption;
    }

    
    /**
     * Set Writer
     *
     * @access  public
     * @param   string writer
     */
    public function setWriter($writer) {
      $this->writer= $writer;
    }

    /**
     * Get Writer
     *
     * @access  public
     * @return  string
     */
    public function getWriter() {
      return $this->writer;
    }

    
    /**
     * Set SupplementalCategories
     *
     * @access  public
     * @param   string supplementalCategories
     */
    public function setSupplementalCategories($supplementalCategories) {
      $this->supplementalCategories= $supplementalCategories;
    }

    /**
     * Get SupplementalCategories
     *
     * @access  public
     * @return  string
     */
    public function getSupplementalCategories() {
      return $this->supplementalCategories;
    }

    
    /**
     * Set SpecialInstructions
     *
     * @access  public
     * @param   string specialInstructions
     */
    public function setSpecialInstructions($specialInstructions) {
      $this->specialInstructions= $specialInstructions;
    }

    /**
     * Get SpecialInstructions
     *
     * @access  public
     * @return  string
     */
    public function getSpecialInstructions() {
      return $this->specialInstructions;
    }

    
    /**
     * Set OriginalTransmissionReference
     *
     * @access  public
     * @param   string originalTransmissionReference
     */
    public function setOriginalTransmissionReference($originalTransmissionReference) {
      $this->originalTransmissionReference= $originalTransmissionReference;
    }

    /**
     * Get OriginalTransmissionReference
     *
     * @access  public
     * @return  string
     */
    public function getOriginalTransmissionReference() {
      return $this->originalTransmissionReference;
    }

    /**
     * Retrieve a string representation
     *
     * @access  public
     * @return  string
     */
    public function toString() {
      return sprintf(
        "  [title                        ] %s\n".
        "  [urgency                      ] %s\n".
        "  [category                     ] %s\n".
        "  [keywords                     ] %s\n".
        "  [dateCreated                  ] %s\n".
        "  [author                       ] %s\n".
        "  [authorPosition               ] %s\n".
        "  [city                         ] %s\n".
        "  [state                        ] %s\n".
        "  [country                      ] %s\n".
        "  [headline                     ] %s\n".
        "  [credit                       ] %s\n".
        "  [source                       ] %s\n".
        "  [copyrightNotice              ] %s\n".
        "  [caption                      ] %s\n".
        "  [writer                       ] %s\n".
        "  [supplementalCategories       ] %s\n".
        "  [specialInstructions          ] %s\n".
        "  [originalTransmissionReference] %s\n".
        "}",  
        $this->title,
        $this->urgency,
        $this->category,
        $this->keywords,
        $this->dateCreated,
        $this->author,
        $this->authorPosition,
        $this->city,
        $this->state,
        $this->country,
        $this->headline,
        $this->credit,
        $this->source,
        $this->copyrightNotice,
        $this->caption,
        $this->writer,
        $this->supplementalCategories,
        $this->specialInstructions,
        $this->originalTransmissionReference
      );
    }
  }
?>
