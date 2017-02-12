<?php
namespace Puffin\Model;

class Project implements ModelInterface
{

    /**
     * @var number
     */
    public $id = null;

    /**
     * @var string
     */
    public $username = '';

    /**
     * @var string
     */
    public $fullName = '';

    /**
     * @var number
     */
    public $version = 1;

    /**
     * @var number
     */
    public $topic = 0;

    /**
     * @var string
     */
    public $status = self::STATUS_UNLOCKED;

    /**
     * @var string
     */
    public $comment = '';

    public $dateUploaded = 0;

    const STATUS_LOCKED = 'locked';
    const STATUS_UNLOCKED = 'unlocked';

    public function setFromAssoc(array $data) {
        if (isset($data['id'])) {
            $this->id = $data['id'];
        }
        if (isset($data['username'])) {
            $this->username = $data['username'];
        }
        if (isset($data['fullName'])) {
            $this->fullName = $data['fullName'];
        }
        if (isset($data['version'])) {
            $this->version = $data['version'];
        }
        if (isset($data['comment'])) {
            $this->comment = $data['comment'];
        }
        if (isset($data['topic'])) {
            $this->topic = $data['topic'];
        }
        if (isset($data['dateUploaded'])) {
            $this->dateUploaded = $data['dateUploaded'];
        }
        if (isset($data['status'])) {
            $this->status = $data['status'];
        }
    }

    public static function fromState(array $state)
    {
        return new self(
            $state['id'],
            $state['username'],
            $state['fullName'],
            $state['version'],
            $state['comment'],
            $state['topic'],
            $state['dateUploaded'],
            $state['status']
        );
    }

    public function toAssoc() {
        $result = [
            'id' => $this->id,
            'username' => $this->username,
            'fullName' => $this->fullName,
            'version' => $this->version,
            'topic' => $this->topic,
            'dateUploaded' => $this->dateUploaded,
            'status' => $this->status,
            'comment' => $this->comment
        ];

        return $result;
    }

    public function isLocked() {
        return $this->status === self::STATUS_LOCKED;
    }

    public function __construct($id, $username, $fullName, $version, $comment, $topic = null, $dateUploaded = 0, $status = 'unlocked')
    {
        $this->id = $id;
        $this->username = $username;
        $this->fullName = $fullName;
        $this->version = $version;
        $this->comment = $comment;
        $this->topic = $topic;
        $this->dateUploaded = $dateUploaded;
        $this->status = $status;
    }
}
