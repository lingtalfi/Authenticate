<?php


namespace Authenticate\Grant;


use Authenticate\BadgeStore\BadgeStoreInterface;
use Authenticate\Grant\Exception\GrantException;
use Authenticate\SessionUser\SessionUser;

class Grantor implements GrantorInterface
{

    /**
     * @var BadgeStoreInterface
     */
    private $badgeStore;
    private $badges;
    private $rootName;


    public function __construct()
    {
        $this->rootName = 'root';
    }

    public static function create()
    {
        return new static();
    }

    public function has($badge)
    {
        if($this->rootName === $badge){
            return true;
        }
        if (null !== $this->badgeStore) {

            if (null !== ($profile = SessionUser::getValue("profile"))) {
                if (null === $this->badges) {
                    $this->badges = array_flip($this->badgeStore->getBadges($profile));
                }
                if (array_key_exists($badge, $this->badges)) {
                    $this->accessGranted($badge);
                    return true;
                }
                $this->accessDenied($badge);
                return false;
            } else {
                $this->badges = null;
            }
            return false;
        }
        $this->error("badgeStore not set");
    }


    public function setBadgeStore(BadgeStoreInterface $badgeStore)
    {
        $this->badgeStore = $badgeStore;
        return $this;
    }

    public function setRootName($rootName)
    {
        $this->rootName = $rootName;
        return $this;
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    protected function error($msg)
    {
        throw new GrantException($msg);
    }

    protected function accessGranted($badge)
    {
    }

    protected function accessDenied($badge)
    {
    }

}