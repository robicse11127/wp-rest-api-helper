<?php
namespace WPRAH\API\Posts;

use WPRAH\API\Posts\PostDate;
use WPRAH\API\Posts\PostTerms;
use WPRAH\API\Posts\AuthorDetails;
use WPRAH\API\Posts\FeaturedImage;

class Posts {

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
        new PostTerms();
    }

}