<?php


class ConvertImageAndSave
{

    //It converts the image file from file to base64 and assigns it to s3.
    public function replaceBase64ImageToFile($html, $class = "")
    {

        preg_replace("/style=\"[^\"]+\"|style='[^']+'/i", "", $html);

        //it get image tags.
        preg_match_all("/(<img src=\"data[^>]+>)/i", $html, $all);


        foreach ($all[0] as $i => $line) {
            if (preg_match("/data:image\/png;base64/", $line)) {
                $ext = ".png";
            } else if (preg_match("/data:image\/jpeg;base64/", $line)) {
                $ext = ".jpg";
            } else {
                throw new Exception(__('error.unsupported_media'));
            }

            $output = [];
            preg_match_all("/base64,.+[^>\"]/i", $line, $output);

            $data = $output[0][0];
            $data = str_replace('base64,', '', $data);

            $data = base64_decode($data); // Decode image using base64_decode

            $path = Str::random(40) . $ext;

            if (Storage::put($path, $data)) {
                $img = '<img ' . ($class ? ' class="' . $class . '" ' : '') . ' src="' . env('FILESYSTEM_URL') . $path . '" />';

                $html = str_replace($all[0][$i], $img, $html);
            }
        }

        return $html;
    }


}