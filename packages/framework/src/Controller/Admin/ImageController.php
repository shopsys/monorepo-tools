<?php

namespace Shopsys\FrameworkBundle\Controller\Admin;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Shopsys\FrameworkBundle\Component\Image\ImageFacade;

class ImageController extends AdminBaseController
{
    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\ImageFacade
     */
    protected $imageFacade;

    /**
     * @param \Shopsys\FrameworkBundle\Component\Image\ImageFacade $imageFacade
     */
    public function __construct(ImageFacade $imageFacade)
    {
        $this->imageFacade = $imageFacade;
    }

    /**
     * @Route("/image/overview/")
     */
    public function overviewAction()
    {
        $imageEntityConfigs = $this->imageFacade->getAllImageEntityConfigsByClass();

        return $this->render('@ShopsysFramework/Admin/Content/Image/overview.html.twig', [
            'imageEntityConfigs' => $imageEntityConfigs,
        ]);
    }
}
