<?php

// In your ServiceProvider model
function calculateRanking($rating)
{
    // Customize the weights as needed
    return $rating * 0.4 ;
}
