<?php
/**
 * load image css and js
 */
class Controller_Assets extends Controller
{

    public function action_load()
    {
        $file = $this->request->param('file');

        // Find the file extension
        $ext = File::getExt($file);

        // Remove the extension from the filename
        $file = substr($file, 0, -(strlen($ext) + 1));

        if ($file_name = Kohana::find_file('media', $file, $ext))
        {
	    // Check if the browser sent an "if-none-match: <etag>" header, and tell if the file hasn't changed
	    $this->response->check_cache(sha1($this->request->uri()) . filemtime($file_name), $this->request);

            // Send the file content as the response
            $this->response->body(file_get_contents($file_name));

            // Set the proper headers to allow caching
            $this->response->headers('content-type', File::mime_by_ext($ext));
            $this->response->headers('last-modified', date('r', filemtime($file_name)));

            // This is ignored by check_cache
            $this->response->headers('cache-control', 'public, max-age=2592000');
        }
        else
        {
            Log::debug('Media controller error while loading file: :file', array(':file' => $file));

            // Return a 404 status
            $this->response->status(404);
        }
    }
}
