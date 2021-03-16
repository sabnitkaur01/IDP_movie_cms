<?php
function addMovie($movie)
{
    try {
        ## TODO: 45 mins ~ 1.5 hrs

        # 1. Connect to the DB
        $pdo = Database::getInstance()->getConnection();

        # 2. Validate the uploaded file
        $cover          = $movie['cover'];
        $upload_file    = pathinfo($cover['name']);
        $accepted_types = array('gif', 'jpg', 'jpe', 'jpeg', 'png', 'svg', 'webp');
        if (!in_array($upload_file['extension'], $accepted_types)) {
            throw new Exception('Wrong file types!');
        }

        # 3. Move the uploaded file around (move the file from the tmp path to the /images)
        $image_path         = '../images/';
        $generated_name     = md5($upload_file['filename'] . time());
        $generated_filename = $generated_name . '.' . $upload_file['extension'];
        $target_path        = $image_path . $generated_filename;
        if (!move_uploaded_file($cover['tmp_name'], $target_path)) {
            throw new Exception('Failed to move uploaded file, check permission!');
        }

        ##(optional) Auto convert user uploads to .webp format
        if (extension_loaded('gd')) {
            switch ($upload_file['extension']) {
                case 'jpg':
                    $upload_source = imagecreatefromjpeg($target_path);
                    break;

                case 'jpeg':
                    $upload_source = imagecreatefromjpeg($target_path);
                    break;
                    
                case 'png':
                    $upload_source = imagecreatefrompng($target_path);
                    break;

                case 'gif':
                    $upload_source = imagecreatefromgif($target_path);
                    break;
            }

            $convert_webp_result = imagewebp($upload_source, $image_path . $generated_name . '.webp');
        }

        # Generate an thumbnail from the original image
        $th_copy = $image_path . 'TH_' . $cover['name'];
        if (!copy($target_path, $th_copy)) {
            throw new Exception('Whoooops, that thumbnail copy did not work!!');
        }

        # 4. Insert into DB (tbl_movies)
        $insert_movie_query = 'INSERT INTO tbl_movies(movies_cover, movies_title, movies_year, movies_runtime, movies_storyline, movies_trailer, movies_release)';
        $insert_movie_query .= ' VALUES(:cover, :title, :year, :run, :story, :trailer, :release)';
        $insert_movie        = $pdo->prepare($insert_movie_query);
        $insert_movie_result = $insert_movie->execute(
            array(
                ':cover'   => $generated_filename,
                ':title'   => $movie['title'],
                ':year'    => $movie['year'],
                ':run'     => $movie['run'],
                ':story'   => $movie['story'],
                ':trailer' => $movie['trailer'],
                ':release' => $movie['release'],
            )
        );

        ## Only when the insert went through, we add the newly created movie to the proper genre
        $last_updated_id = $pdo->lastInsertId();
        if (!empty($last_updated_id) && $insert_movie_result) {
            $update_genre_query = 'INSERT INTO tbl_mov_genre(movies_id, genre_id) VALUES (:movies_id, :genre_id)';
            $update_genre       = $pdo->prepare($update_genre_query);
            $update_genre->execute(
                array(
                    ':movies_id' => $last_updated_id,
                    ':genre_id'  => $movie['genre'],
                )
            );
        }

        # 5. If all of above works, redirect user to index.php
        redirect_to('index.php');
    } catch (Exception $e) {
        # Return a error message
        $error = $e->getMessage();
        return $error;
    }

}

function getAllMovieGenres()
{
    $pdo = Database::getInstance()->getConnection();

    $get_genre_query = 'SELECT * FROM tbl_genre';
    $genres          = $pdo->query($get_genre_query);

    if ($genres) {
        return $genres;
    } else {
        return false;
    }
}
