<?php
namespace LilWorks\StoreBundle\Form\Transformer;
use LilWorks\StoreBundle\Entity\Picture;
use Symfony\Component\Form\DataTransformerInterface;
class PicturesTransformer implements DataTransformerInterface
{
    public function transform($value)
    {
        // TODO: Implement transform() method.
        echo "TRANSFORMER transform";

        var_dump(count($value->getPictures()));

    }

    public function reverseTransform($files)
    {
        echo "TRANSFORMER reverseTransform";
        var_dump(get_class($files));

        $attachments = [];
        /*
        foreach($files as $file){
            $attachment = new Picture();
            //$attachment->setFile($file);
            //$attachments[] = $attachment;
            var_dump(get_class($file));
        }

        die();
*/
        return $attachments;
    }
}