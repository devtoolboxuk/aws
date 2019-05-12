<?php

namespace devtoolboxuk\aws;

class aws
{
    public function s3()
    {
        return new AwsS3();
    }
    
}