<?php
/**
 * This class represents the a more flexible menu used to group actions.
 *
 * @author  Timo Hartge <hartge@data-quest.de>
 * @license GPL2 or any later version
 * @since   Stud.IP 4.0
 */
final class AvatarMenu extends ActionMenu
{
    /**
     * Creates the avatar menu for a specific user
     */
    public static function forUser(User $user): self
    {
        $menu = self::get();
        $menu->addCSSClass('avatar-menu');
        $menu->addAttribute('data-action-menu-reposition', 'false');
        $menu->setContext($user->getFullName());
        $menu->setTitle(_('Profilmenü'));
        $menu->setImage(
            Avatar::getAvatar($user->id)->getImageTag(),
            ['id' => 'header_avatar_image_link']
        );

        return $menu;
    }

    /**
     * Adds the menu items from a given navigation path
     */
    public function withNavigation(string $path): self
    {
        foreach (Navigation::getItem($path) as $subpath => $subnav) {
            if ($subnav->getRenderAsButton()) {
                $this->addButton(
                    $subpath,
                    $subnav->getTitle(),
                    $subnav->getImage(),
                    array_merge(
                        $subnav->getLinkAttributes(),
                        ['formaction' => URLHelper::getURL($subnav->getURL(), [], true)]
                    )
                );
            } else {
                $this->addLink(
                    URLHelper::getURL($subnav->getURL(), [], true),
                    $subnav->getTitle(),
                    $subnav->getImage(),
                    $subnav->getLinkAttributes()
                );
            }
        }

        return $this;
    }

    /**
     * Ensures that the menu is always rendered as a menu.
     */
    #[Override]
    public function getRenderingMode(): string
    {
        return self::RENDERING_MODE_MENU;
    }

    /**
     * Sets the image of the menu
     */
    public function setImage(string $image_html, array $image_attributes = []): self
    {
        $this->image = $image_html;
        $this->image_attributes = $image_attributes;

        return $this;
    }
}
