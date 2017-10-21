<?php
namespace LilWorks\StoreBundle\Form\Transformer;
use LilWorks\StoreBundle\Entity\Picture;
use Symfony\Component\Form\DataTransformerInterface;
class PicturesTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        // TODO: Implement transform() method.
    }

    public function reverseTransform($files)
    {
        $pictures = [];
        foreach($files as $file){
            var_dump($file);
            echo "<hr>";
            $picture = new Picture();
            $picture->setPictureFile($file);
            $pictures[] = $picture;
        }
        return $pictures;
    }
}