<?php
namespace WPRAH\API\Pages;

use WPRAH\API\Pages\PostDate;
use WPRAH\API\Pages\AuthorDetails;
use WPRAH\API\Pages\FeaturedImage;

class Pages {

    /**
    * Construct Function
    * @since 2.0.0
    */
    public function __construct() {
        $this->init();
    }

    /**
    * Init Function
    * @since 2.0.0
    */
    public function init() {
        new FeaturedImage();
        new AuthorDetails();
        new PostDate();
    }

}