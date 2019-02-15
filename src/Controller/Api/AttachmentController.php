<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 1/18/19
 * Time: 16:37
 */

namespace App\Controller\Api;

use App\Entity\Attachment;
use App\Entity\Item;
use App\Exception\JsonHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AttachmentController extends AbstractController
{
    /**
     * @Route("/api/item/{item}/attachment", methods={"POST"})
     */
    public function addAction(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, Item $item)
    {
        if (!$content = $request->getContent()) {
            throw new JsonHttpException(400, 'Bad Request');
        }
        $data = json_decode($content, true);
        if (!isset($data['text'])) {
            throw new JsonHttpException(400, 'Bad Request');
        }
        $attachment = $serializer->deserialize($content, Attachment::class, 'json');
        $errors = $validator->validate($attachment);
        if (count($errors)) {
            throw new JsonHttpException(400, 'Bad Request');
        }
        $item->setAttachment($attachment);
        $em = $this->getDoctrine()->getManager();
        $em->persist($item);
        $em->flush();

        return $this->json($item);
    }

    /**
     * @Route("/api/item/{item}/attachment/{attachment}", methods={"DELETE"})
     */
    public function removeAction(Item $item, Attachment $attachment)
    {
        if (!$attachment === $item->getAttachment()) {
            throw new JsonHttpException(400, 'Bad Request');
        }
        $item->setAttachment(null);
        $em = $this->getDoctrine()->getManager();
        $em->remove($attachment);
        $em->persist($item);
        $em->flush();

        return $this->json(null, 200);
    }
}
