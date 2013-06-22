<?


class Inc_Image{
			
	
	
	// NO PICTURE CHECK ////////////////////////////////////////////////////////////
	// We will assume the user has no picture by default -- the next few checks will reverse that if necessary.
	//
	//
	//
	//
	//
	// DO NOT EDIT THIS FILE WHILE REWRITE DEVELOPMENT PLEASE EDIT IN /tmp/include_src 
	//
	//
	//
	//
	//
	public static function picHandle($inputFile,$CONF_EXT)
	{
		
		$CONF["PICTURES_ALLOWED_EXTENTIONS"] 					= "gif,jpg,jpeg,png,bmp";
		$CONF["IMAGE_ENABLE_PROCESSOR:GD"] 						= 1;
		$CONF["IMAGE_PROCESSOR:GD2"] 							= 1;
		$CONF["IMAGE_PROCESSOR_DEBUG_MODE"] 					= 0;
		$CONF["IMAGE_FORCE_CONSTRAIN_PROPORTIONS"]	 			= 1;
		$CONF["IMAGE_CONSTRAIN_PROPORTIONS_ASPECT_RATIO"] 		= 1;
		$CONF["IMAGE_MAX_FILE_SIZE"] 							= 100000000;
		$CONF["IMAGE_HEADER_STRING"] 							= "Content-type: image/jpeg";
		$CONF["IMAGE_CACHE_PROCESSED"] 							= 1;
		$CONF["IMAGE_CACHE_DISPLAY:USE_FORWARD"] 				= 1;
		$CONF["IMAGE_USE_STAMP_TEXT"] 							= 0;
		$CONF["IMAGE_STAMP_TEXT"] 								= "Fashion Profiles";
		$CONF["IMAGE_STAMP_TEXT_COLOR"] 						= "0,0,0";
		$CONF["IMAGE_STAMP_TEXT_SIZE"] 							= 10;
		$CONF["IMAGE_STAMP_TEXT_LOCATION_Y"] 					= "top";
		$CONF["IMAGE_STAMP_TEXT_LOCATION_X"] 					= "left";
		$CONF["IMAGE_STAMP_TEXT_PADDING_Y"] 					= 5;
		$CONF["IMAGE_STAMP_TEXT_PADDING_X"]			 			= 5;
		$CONF["IMAGE_STAMP_TEXT_DROPHILIGHT"] 					= 1;
		$CONF["IMAGE_STAMP_TEXT_DROPHILIGHT_DEPHASE"] 			= 1;
		$CONF["IMAGE_STAMP_TEXT_DROPHILIGHT_COLOR"] 			= "255,255,255";
		$CONF["IMAGE_QUALITY"] 									= 100;
		$CONF["IMAGE_MAX_WIDTH"] 								= 5000;
		$CONF["IMAGE_THUMBNAILS_SIZE"] 							= 100;
		$CONF["IMAGE_STAMP_MINWIDTH"] 							= 100;
		$CONF["IMAGE_USE_WATERMARK"] 							= 0;
		$CONF["IMAGE_WATERMARK_FILE"] 							= "";
		$CONF["IMAGE_WATERMARK_PADDING"] 						= 10;
		$CONF["IMAGE_WATERMARK_RESIZE_FACTOR"] 					= 40;
		$CONF["IMAGE_WATERMARK_MINWIDTH"] 						= 100;
		$CONF["IMAGE_WATERMARK_BLEND_VISIBILITY"] 				= 80;
		$CONF["IMAGE_INPUT_WIDTH"] 								= null;
		$CONF["IMAGE_PROCESS_MODE"] 							= "resizecrop";
		$CONF["IMAGE_NOFILE_DEFAULT_FILE"] 						= prepath()."default/nopicture.gif";
		$CONF["IMAGE_BACK_GROUND_COLOR_FILL"]					= "255,255,255";
		$CONF["IMAGE_THUMBNAILS_SIZE"] 							= 100;
		$CONF["IMAGE_DEFAULT_DIRECTORY"] 						= ROOT_PATH."/photo";
		$CONF["IMAGE_PREFIX"] 									= "small_";		
		foreach($CONF_EXT as $k => $v){
			$CONF[$k] = $v;
		}
		//Inc_Var::vardump($CONF);
		//exit();
		
		$USE_NOPICTURE = true;
		// The file to be loaded is passed in the URL,we will make sure a file name has been passed.
		if (!empty($inputFile))
		{
			// We will also prevent "slashes" in the filename as a protection (so they can't use the ../ call to force this to climb up in the directory tree
			if (!strpos($inputFile, "/"))
			{
				// Check the file name extention gainst the allowed list. We won't work with unallowed stuff
				$fileNameChunks = explode(".", $inputFile);
				// $CONF["PICTURES_ALLOWED_EXTENTIONS"] ้ป�่ฎคๅ�ฏไพ�ๆ“�ไฝ�็��ๅ�พ็��ๆ ผๅผ�
				if (in_array($fileNameChunks[count($fileNameChunks)-1], explode(",", $CONF["PICTURES_ALLOWED_EXTENTIONS"])))
				{
					// We will take a look out and see if the file exists
					// $CONF["IMAGE_DEFAULT_DIRECTORY"] ๅ�พ็��้ป�่ฎค็�ฎๅฝ•
					if (is_file($CONF["IMAGE_DEFAULT_DIRECTORY"]."/{$inputFile}"))
					{
						// Don't process pictures that could cause potential errors because of their size (max allocation.e)
						// $CONF["IMAGE_MAX_FILE_SIZE"]ใ€€ไฝฟ็”จๅ�พ็��็��ๆ�€ๅคงๅคงๅฐ�
						if (filesize($CONF["IMAGE_DEFAULT_DIRECTORY"]."/{$inputFile}") < $CONF["IMAGE_MAX_FILE_SIZE"])
						{
							// We're over all the checks, let's set the following variable so the processor don't take the "nopicture" file.
							$USE_NOPICTURE = false;
						}
					}
				}
			}
		}
	
		// Set the $fileName variable for future use
		// $CONF["IMAGE_NOFILE_DEFAULT_FILE"] ๅฆ�ๆ��ๅ�พ็��ไธ�ๅญ�ๅ�จไฝฟ็”จ็��ๅ�พ็��
		if ($USE_NOPICTURE) $fileName = $CONF["IMAGE_NOFILE_DEFAULT_FILE"];
		else $fileName = $inputFile;
		// PICTURE WIDTH VARIABLE //////////////////////////////////////////////////////
		// Now we will check if a picture size has been passed. If not, we will consider that we must process a thumbnail
		if (isset($CONF['IMAGE_INPUT_WIDTH']) and is_numeric($CONF['IMAGE_INPUT_WIDTH']) and $CONF['IMAGE_INPUT_WIDTH'] > 0)
		{
			// We won't process a wallpaper-sized picture, let's make sure we don't go over the max width setting
			// $CONF["IMAGE_MAX_WIDTH"] ๅ�พ็��ๆ�€ๅคงๅฎฝๅบฆ 600
			// $CONF["IMAGE_THUMBNAILS_SIZE"] ๅ�พ็��็ผฉ็•ฅๅ�พๅคงๅฐ� 90
			if ($CONF['IMAGE_INPUT_WIDTH'] < $CONF["IMAGE_MAX_WIDTH"]) $newSize = $CONF['IMAGE_INPUT_WIDTH'];
			else $newSize = $CONF["IMAGE_MAX_WIDTH"];
		} else $newSize = $CONF["IMAGE_THUMBNAILS_SIZE"];
		// Ok so no width? Let's make a thumbnail!
	
		// LOAD PICTURE FILE CONTENT & STREAM CACHE ////////////////////////////////////
		// Let's first see if we've got a cached version and if we're configured to use the cache.
		// $CONF["IMAGE_CACHE_PROCESSED"]ใ€€ๅ�พ็��็ผ“ๅญ�ใ€€1
		if ($CONF["IMAGE_CACHE_PROCESSED"])
		{
			// We will now check if a cache of the called image exists.
			if (is_file($CONF["IMAGE_DEFAULT_DIRECTORY"]."/{$CONF['IMAGE_PREFIX']}{$fileName}"))
			{
				// Configuration says: forward the user to the picture if we've got a cache! Let's do it...
				if ($CONF["IMAGE_CACHE_DISPLAY:USE_FORWARD"])
				{
					// header("Location: ".$CONF["IMAGE_DEFAULT_DIRECTORY"]."/{$CONF["IMAGE_PREFIX"]}{$fileName}".".jpg");
					// Don't go any further, we're done.
					// die();
				}
				// We've been instructed to STREAM the file insteadof forwarding the user to it... how about doing it?
				else {
					// header($CONF["IMAGE_HEADER_STRING"]);
					// echo file_get_contents($CONF["IMAGE_DEFAULT_DIRECTORY"]."/{$CONF["IMAGE_PREFIX"]}{$fileName}".".jpg");
					// Don't go any further, we're done.
					// die();
				}
			}
			// There was no cache file, we will have to process the original
			else{
				//Inc_Var::vardump($CONF["IMAGE_DEFAULT_DIRECTORY"]."/{$fileName}");
				//exit();
				$fileBuffer = file_get_contents($CONF["IMAGE_DEFAULT_DIRECTORY"]."/{$fileName}");
			}
		}
		// We've been instructed not to use cache ... We will buffer the original image
		$fileBuffer = file_get_contents($CONF["IMAGE_DEFAULT_DIRECTORY"]."/{$fileName}");
		// If the processor is disabled, we will just stream what we buffered...
		if (!$CONF["IMAGE_ENABLE_PROCESSOR:GD"])
		{
			// header($CONF["IMAGE_HEADER_STRING"]);
			// echo $fileBuffer;
			// Don't go any further, we're done.
			// die();
		}
		// PROCESSOR ///////////////////////////////////////////////////////////////////
		// Load the original file into an image handle
		switch(substr($fileName, strrpos($fileName, ".")+1, strlen($fileName)))
		{
			// Jpeg Image
			case("jpg"): case("jpeg"):
				$handle = imagecreatefromjpeg($CONF["IMAGE_DEFAULT_DIRECTORY"]."/{$fileName}");
				break;
			// GIF image, note that we got two methods for loading it here, one using the GD1 engine, one with GD2.
			case("gif"):
				$tempHandle = imagecreatefromgif($CONF["IMAGE_DEFAULT_DIRECTORY"]."/{$fileName}");
				if ($CONF["IMAGE_PROCESSOR:GD2"]) $handle = imagecreatetruecolor(imagesx($tempHandle), imagesy($tempHandle));
				else $handle = imagecreate($newSize, $processHeight);
				imagecopy($handle, $tempHandle, 0, 0, 0, 0, imagesx($tempHandle), imagesy($tempHandle));
				break;
			// Png image
			case("png"):
				$handle = imagecreatefrompng($CONF["IMAGE_DEFAULT_DIRECTORY"]."/{$fileName}");
				break;
		}
		// Process to crop, fill or resize
		switch($CONF["IMAGE_PROCESS_MODE"])
		{
			case("resize"):
				//In resize mode, we will only find the source picture biggest size axis (W/H) and resize that max value to the required processed size, the other axix is resized acordingly
				list($sourceWidth, $sourceHeight) = getimagesize($CONF["IMAGE_DEFAULT_DIRECTORY"]."/{$fileName}");
				if ($sourceWidth >= $sourceHeight) $ratio = $newSize / $sourceWidth;
				else $ratio = $newSize / $sourceHeight;
				$thumb = imagecreatetruecolor(round($sourceWidth * $ratio), round($sourceHeight * $ratio));
					imagecopyresampled(
						$thumb, $handle, 0,0,0,0,
						round($sourceWidth * $ratio),
						round($sourceHeight * $ratio),
						$sourceWidth, $sourceHeight
					);
				break;
			case("crop"): default:
				// The crop mode picks the lowest source axis as its reference,the second axis is centered and cropped at constrained proportions values
				$thumb = imagecreatetruecolor(
					$newSize,
					$newHeight = round($newSize * $CONF["IMAGE_CONSTRAIN_PROPORTIONS_ASPECT_RATIO"])
				);
				list($sourceWidth, $sourceHeight) = getimagesize($CONF["IMAGE_DEFAULT_DIRECTORY"]."/{$fileName}");
				if ($sourceWidth >= $sourceHeight)
				{
					$intraSourceWidth = round($sourceHeight / $CONF["IMAGE_CONSTRAIN_PROPORTIONS_ASPECT_RATIO"]);
					imagecopyresampled(
						$thumb, $handle, 0, 0,
						($sourceWidth / 2) - ($intraSourceWidth / 2),
						0,$newHeight,$intraSourceWidth,$sourceHeight
					);
				} else {
					$intraSourceHeight = round($sourceWidth * $CONF["IMAGE_CONSTRAIN_PROPORTIONS_ASPECT_RATIO"]);
					imagecopyresampled(
						$thumb, $handle, 0, 0, 0,
						($sourceHeight / 2) - ($intraSourceHeight / 2),
						$newSize, $newHeight,$sourceWidth,$intraSourceHeight
					);
				}
				break;
			case("fill"):
				// Fill mode resizes the original picture according to its highest axis to fit the thumbnail size. The lower axis is then used to center the result into the thumbnail
				$thumb = imagecreatetruecolor($newSize, $newHeight = round($newSize * $CONF["IMAGE_CONSTRAIN_PROPORTIONS_ASPECT_RATIO"]));
				$color = explode(",", $CONF["IMAGE_BACK_GROUND_COLOR_FILL"]);
				$fillColor = imagecolorallocate($thumb, $color[0], $color[1], $color[2]);
	
	 			ImageFill($thumb, 0, 0, $fillColor); //Fill background color for php5
	
				list($sourceWidth, $sourceHeight) = getimagesize($CONF["IMAGE_DEFAULT_DIRECTORY"]."/{$fileName}");
	
				imagefilledrectangle($thumb, 0, 0, $sourceWidth, $sourceHeight,$fillColor); //Fill background color for php4
	
				if ($sourceWidth >= $sourceHeight)
				{
					$ratio = $newSize / $sourceWidth;
					$intraSourceWidth = $newSize;
					$intraSourceHeight = $sourceHeight * $ratio;
					imagecopyresampled(
						$thumb,$handle,0,
						($newHeight / 2) - ($intraSourceHeight / 2),
						0,0,$newSize,
						ceil($intraSourceHeight),$sourceWidth,$sourceHeight
					);
				} else {
					$ratio = $newHeight / $sourceHeight;
					$intraSourceHeight = $newHeight;
					$intraSourceWidth = $sourceWidth * $ratio;
					imagecopyresampled(
						$thumb,$handle,
						($newSize / 2) - ($intraSourceWidth / 2),
						0,0,0,
						ceil($intraSourceWidth),
						$newHeight,$sourceWidth,$sourceHeight
					);
				}
				break;
			case("resizecrop"):
				// Fill mode resizes the original picture according to its highest axis to fit the thumbnail size. The lower axis is then used to center the result into the thumbnail
				$thumb = imagecreatetruecolor($newSize, $newHeight = round($newSize * $CONF["IMAGE_CONSTRAIN_PROPORTIONS_ASPECT_RATIO"]));
				$color = explode(",", $CONF["IMAGE_BACK_GROUND_COLOR_FILL"]);
				$fillColor = imagecolorallocate($thumb, $color[0], $color[1], $color[2]);
	
	 			ImageFill($thumb, 0, 0, $fillColor); //Fill background color for php5
	
				list($sourceWidth, $sourceHeight) = getimagesize($CONF["IMAGE_DEFAULT_DIRECTORY"]."/{$fileName}");
	
				imagefilledrectangle($thumb, 0, 0, $sourceWidth, $sourceHeight,$fillColor); //Fill background color for php4
	
				if ($sourceWidth >= $sourceHeight)
				{
					$ratio = $newSize / $sourceHeight;
					$intraSourceWidth = $newSize;
					$intraSourceHeight = $sourceHeight * $ratio;
					imagecopyresampled(
						$thumb,$handle,0,0,//($newHeight / 2) - ($intraSourceHeight / 2),
						$sourceWidth/2-$sourceHeight/2,0,
						$newSize,$newSize,
						$sourceHeight,$sourceHeight
					);
				} else {
					$ratio = $newHeight / $sourceHeight;
					$intraSourceHeight = $newHeight;
					$intraSourceWidth = $sourceWidth * $ratio;
					imagecopyresampled(
						$thumb,$handle,
						0,0,
						0,$sourceHeight/2-$sourceWidth/2,
						$newSize,$newSize,
						$sourceWidth,$sourceWidth
					);
				}
				break;
			case("fix_wh_align_top"):
				// Fill mode resizes the original picture according to its highest axis to fit the thumbnail size. The lower axis is then used to center the result into the thumbnail
				$newWidth = $CONF["IMAGE_OUTPUT_WIDTH"];
				$newHeight = $CONF["IMAGE_OUTPUT_HEIGHT"];
				$thumb = imagecreatetruecolor($newWidth	,$newHeight);
				$color = explode(",", $CONF["IMAGE_BACK_GROUND_COLOR_FILL"]);
				$fillColor = imagecolorallocate($thumb, $color[0], $color[1], $color[2]);
	
	 			ImageFill($thumb, 0, 0, $fillColor); //Fill background color for php5
	
				list($sourceWidth, $sourceHeight) = getimagesize($CONF["IMAGE_DEFAULT_DIRECTORY"]."/{$fileName}");
	
				imagefilledrectangle($thumb, 0, 0, $sourceWidth, $sourceHeight,$fillColor); //Fill background color for php4
	
				$ratio = $newWidth / $sourceWidth;
				$intraSourceHeight = $sourceHeight * $ratio;
				imagecopyresampled(
					$thumb,$handle,0,0,0,0,
					$newWidth,$sourceHeight*$ratio,
					$sourceWidth,$sourceHeight
				);


				break;
			case("fixwidth"):
				// Fill mode resizes the original picture according to its highest axis to fit the thumbnail size. The lower axis is then used to center the result into the thumbnail
			
				list($sourceWidth, $sourceHeight) = getimagesize($CONF["IMAGE_DEFAULT_DIRECTORY"]."/{$fileName}");
				$newHeight = round($newSize * $CONF["IMAGE_CONSTRAIN_PROPORTIONS_ASPECT_RATIO"]);
				//if($sourceHeight>$sourceWidth){
				$newHeight = $sourceHeight*$newSize/$sourceWidth;
				//}else{
					
				//}
				
				$thumb = imagecreatetruecolor($newSize,$newHeight );
				//$color = explode(",", $CONF["IMAGE_BACK_GROUND_COLOR_FILL"]);
				//$fillColor = imagecolorallocate($thumb, $color[0], $color[1], $color[2]);
	
	 			//ImageFill($thumb, 0, 0, $fillColor); //Fill background color for php5
	
	
				//imagefilledrectangle($thumb, 0, 0, $sourceWidth, $sourceHeight,$fillColor); //Fill background color for php4
	
				if ($sourceWidth >= $sourceHeight)
				{
					$ratio = $newSize / $sourceWidth;
					$intraSourceWidth = $newSize;
					$intraSourceHeight = $sourceHeight * $ratio;
					imagecopyresampled(
						$thumb,$handle,0,
						($newHeight / 2) - ($intraSourceHeight / 2),
						0,0,$newSize,
						ceil($intraSourceHeight),$sourceWidth,$sourceHeight
					);
				} else {
					//$ratio = $newSize / $sourceWidth;
					//$intraSourceHeight = $newHeight;
					//$intraSourceWidth = $sourceWidth * $ratio;
					imagecopyresampled(
						$thumb,$handle,
						0,0,
						0,0,
						$newSize,$newHeight,
						$sourceWidth,$sourceHeight
					);
				}
				break;						
		}
		// IMAGE STAMP /////////////////////////////////////////////////////////////////
		if (($CONF["IMAGE_USE_STAMP_TEXT"]) && ($CONF["IMAGE_STAMP_TEXT"] != ""))
		{
			// Lets just make sure this thumbnail is big enough to welcome the stamp text.
			if ($newSize >= $CONF["IMAGE_STAMP_MINWIDTH"])
			{
				// Now let's see where to place that stamp, The following switches will give a startup position for the stamp X/Y coords
				switch($CONF["IMAGE_STAMP_TEXT_LOCATION_Y"])
			    {
					case("top"):
						$stampLocationY = 0;
						break;
					case("middle"):
						$stampLocationY = ($processHeight / 2) - ($CONF["IMAGE_STAMP_TEXT_SIZE"]/2);
						break;
					case("bottom"):
						$stampLocationY = $processHeight - $CONF["IMAGE_STAMP_TEXT_SIZE"];
						break;
				}
				switch($CONF["IMAGE_STAMP_TEXT_LOCATION_X"])
				{
					case("left"):
						$stampLocationX = 0;
						break;
					case("middle"):
						$stampLocationX = ($newSize / 2) - ((strlen($CONF["IMAGE_STAMP_TEXT"]) * $CONF["IMAGE_STAMP_TEXT_SIZE"])/2);
						break;
					case("right"):
						$stampLocationX = $newSize - (strlen($CONF["IMAGE_STAMP_TEXT"]) * $CONF["IMAGE_STAMP_TEXT_SIZE"]);
						break;
				}
				// Now we add the padding values!
				$stampLocationX = $stampLocationX + $CONF["IMAGE_STAMP_TEXT_PADDING_X"];
				$stampLocationY = $stampLocationY + $CONF["IMAGE_STAMP_TEXT_PADDING_Y"];
				// Are we supposed to add a drop shadow hilight on that?
				if ($CONF["IMAGE_STAMP_TEXT_DROPHILIGHT"])
				{
					// Yep! First thing first; find the color code and attribute it to the handler.
					$color = explode(",", $CONF["IMAGE_STAMP_TEXT_DROPHILIGHT_COLOR"]);
					$dropColor = imagecolorallocate($thumb, $color[0], $color[1], $color[2]);
					// .. and add the string to the image (as this is a hilight and that we want it to be under the other text, we will set it first. How Logic!
					imagestring(
						$thumb, $CONF["IMAGE_STAMP_TEXT_SIZE"],
						$stampLocationX + $CONF["IMAGE_STAMP_TEXT_DROPHILIGHT_DEPHASE"],
						$stampLocationY + $CONF["IMAGE_STAMP_TEXT_DROPHILIGHT_DEPHASE"],
						$CONF["IMAGE_STAMP_TEXT"], $dropColor);
				}
				// Now we will add the top layer stamp. Let's find that color code and attribute it to the handler.
				$color = explode(",", $CONF["IMAGE_STAMP_TEXT_COLOR"]);
				$stampColor = imagecolorallocate($thumb, $color[0], $color[1], $color[2]);
				// ... and text that!
				imagestring(
						$thumb, $CONF["IMAGE_STAMP_TEXT_SIZE"],
						$stampLocationX, $stampLocationY,
						$CONF["IMAGE_STAMP_TEXT"], $stampColor
					);
			}
		}
	
		// IMAGE WATERMARK /////////////////////////////////////////////////////////////
		if ($CONF["IMAGE_USE_WATERMARK"] and imagesx($thumb) >= $CONF["IMAGE_WATERMARK_MINWIDTH"])
		{
			// Load watermark
			list($srcmarkwidth, $srcmarkheight) = getimagesize("cache/pictures/".$CONF["IMAGE_WATERMARK_FILE"]);
			$tempwmhandler = imagecreatefrompng("cache/pictures/".$CONF["IMAGE_WATERMARK_FILE"]);
			if ($srcmarkwidth >= $srcmarkheight)
			{
				$wmwidth = (imagesx($thumb) * $CONF["IMAGE_WATERMARK_RESIZE_FACTOR"]) / 100;
				$wmheight = round($srcmarkheight * ($wmwidth / $srcmarkwidth));
			} else {
				$wmheight = (imagesy($thumb) * $CONF["IMAGE_WATERMARK_RESIZE_FACTOR"]) / 100;
				$wmwidth = round($markheight * ($wmheight / $srcmarkheight));
			}
			$watermark = imagecreatetruecolor($wmwidth, $wmheight);
			imagecopyresampled($watermark, $tempwmhandler, 0, 0, 0, 0, $wmwidth, $wmheight, $srcmarkwidth, $srcmarkheight);
			imagecolortransparent($watermark, imagecolorallocate($watermark, 0, 0, 0));
			imagealphablending($watermark, true);
			// Put watermark over the picture
			imagecopymerge(
				$thumb,$watermark,
				imagesx($thumb) - $wmwidth - $CONF["IMAGE_WATERMARK_PADDING"],
				imagesy($thumb) - $wmheight - $CONF["IMAGE_WATERMARK_PADDING"],
				0, 0, $srcmarkwidth, $srcmarkheight,
				$CONF["IMAGE_WATERMARK_BLEND_VISIBILITY"]
			);
			imagedestroy($watermark);
		}
		// SAVE CACHE //////////////////////////////////////////////////////////////////
		// Ok now we want to save a cached version of whatwe processed... well - Do we?
		if ($CONF["IMAGE_CACHE_PROCESSED"])
		{
			// Simple as one, two, sixteen. We save the cached result in a jpeg file!
			
			$cacheFileDir = dirname($fileName);
			$cacheFilename = $CONF["IMAGE_PREFIX"].basename($fileName);
			$cacheFilePath = $cacheFileDir."/".$cacheFilename;
			
			imagejpeg(
				$thumb, $CONF["IMAGE_DEFAULT_DIRECTORY"]."/{$cacheFilePath}",
				$CONF["IMAGE_QUALITY"]
			);
		}
		// STREAM BUFFER ///////////////////////////////////////////////////////////////
		// Now we will stream the image to the browser.
	
		if(!$CONF["IMAGE_PROCESSOR_DEBUG_MODE"])
		{
			// header($CONF["IMAGE_HEADER_STRING"]);
		}
		// imagejpeg($thumb, false, $CONF["IMAGE_QUALITY"]);
	
		// And clean the mess ;)
		imagedestroy($thumb);
		imagedestroy($handle);
	
		return ;
	}
	
		
}