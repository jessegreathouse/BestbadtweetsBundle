<?

namespace Jessegreathouse\Bundle\BestbadtweetsBundle\Extension;

use Jessegreathouse\Bundle\BestbadtweetsBundle\Entity\User;

class LikeTwigExtension extends \Twig_Extension {

    public function getFunctions()
    {
        return array(
            'user_liked' => new \Twig_Function_Method($this, 'userLiked'),
        );
    }

    public function userLiked($user, $commentVotes) {
        if (!$user instanceof User) {
            return false;
        }
        $found = false;
        $user_id = $user->getId();
        foreach ($commentVotes as $vote) {
            if ($vote->getUser()->getId() == $user_id) {
                $found = true;
                break;
            }
        }
        return $found;
    }

    public function getName()
    {
        return 'like';
    }

}
