<?php

class gssmRender {

    private $gssmStripe;

    public function __construct($gssmStripe) {
        $this->gssmStripe = $gssmStripe;
    }

    public function connect_button($text){

        $url = $this->gssmStripe->get_connect_url();
        if ($url){
            echo '<a href="' . $url . '">' . $text . '</a>';
        }

    } 


}

$gssmStripe = new gssmStripe(GSSM_STRIPE_PUBLIC);
$gssmRender = new gssmRender($gssmStripe);

?>