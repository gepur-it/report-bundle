<?php
/**
 * @author Marina Mileva <m934222258@gmail.com>
 * @since 22.11.17
 */

namespace GepurIt\ReportBundle;

use GepurIt\ReportBundle\DependencyInjection\ReportExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class ReportBundle
 * @package ReportBundle
 */
class ReportBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }

    /**
     * Register Bundle Extension
     * @return ReportExtension
     */
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new ReportExtension();
        }

        return $this->extension;
    }
}
