<?php

namespace App\Models;
require 'C:\xampp\htdocs\firebase\vendor\autoload.php';

use CodeIgniter\Model;
use Google\Cloud\Core\ServiceBuilder;
use Google\Cloud\Firestore\FirestoreClient;
use App\Models\misc;

class firebaseConnect extends Model
{
    public function gConnect()
    {
        $gcloud = new ServiceBuilder([
            'keyFile' => json_decode(file_get_contents('C:\xampp\htdocs\firebase\keyfile.json'), true)
        ]);
        $storage = $gcloud->storage();

        $bucket = $storage->bucket('myBucket');
        log_message(7, print_r($bucket, TRUE));
    }

    public function fireStore($key)
    {
        $misc = new misc();
        $firestore = new FirestoreClient([
            'projectId' => 'trion-cb347',
            'keyFile' => json_decode(file_get_contents('C:\xampp\htdocs\firebase\keyfile.json'), true)
        ]);

        $collectionReference = $firestore->collection('songs');

        $rangeQuery = $collectionReference
            ->where('song_name', '>=', $key)
            ->where('song_name', '<', $key . "z");
        $query = $rangeQuery->limit(4);
        $snapshot = $query->documents();
        $rsp = array();

        foreach ($snapshot as $document) {
            $song = array();
            if ($document->exists()) {
                $song['Added_by'] = $document->data()['Added_by'] == null ? "No Date" : $document->data()['Added_by'];
                $song['hits'] = $document->data()['hits'] == null ? "No Date" : $document->data()['hits'];
                $song['img_url'] = $document->data()['img_url'] == null ? "No Date" : $document->data()['img_url'];
                $song['language'] = $document->data()['language'] == null ? "No Date" : $document->data()['language'];
                $song['song_name'] = $document->data()['song_name'] == null ? "No Date" : $document->data()['song_name'];
                $song['song_url'] = $document->data()['song_url'] == null ? "No Date" : $document->data()['song_url'];
                $song['song_id'] = $document->data()['song_id'] == null ? "No Date" : $document->data()['song_id'];
                $song['template_url'] = $document->data()['template_url'] == null ? "No Date" : $document->data()['template_url'];
                $song['artists'] = $document->data()['artist_name'] == null ? array("No Date") : $document->data()['artist_name'];
            } else {
                log_message(7, printf('Document %s does not exist!' . PHP_EOL, $document->id(), TRUE));
            }
            $errorData = $misc->count_array_values($song, 'No Date');
            if ($errorData > 0) {
                continue;
            }
            $rsp[] = $song;
        }

        if (sizeof($rsp) < 1) {
            $song['Added_by'] = "No Date";
            $song['hits'] = 0;
            $song['img_url'] = "No Date";
            $song['language'] = "No Date";
            $song['song_name'] = "No Date";
            $song['song_url'] = "No Date";
            $song['song_id'] = "No Date";
            $song['template_url'] = "No Date";
            $song['artists'] = array("No Date");
            $rsp[] = $song;
        }
        return $rsp;
    }
}