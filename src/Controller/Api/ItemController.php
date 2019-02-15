<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 1/18/19
 * Time: 15:52
 */

namespace App\Controller\Api;

use App\Entity\CheckList;
use App\Entity\Item;
use App\Exception\JsonHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ItemController extends AbstractController
{
    /**
     * @Route("/api/list/{id}/item", methods={"POST"})
     */
    public function addAction(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, CheckList $checkList)
    {
        if (!$content = $request->getContent()) {
            throw new JsonHttpException(400, 'Bad Request');
        }
        $data = json_decode($content, true);
        if (!isset($data['checked'])) {
            throw new JsonHttpException(400, 'Bad Request');
        }
        $item = $serializer->deserialize($request->getContent(), Item::class, 'json');
        $errors = $validator->validate($item);
        if (count($errors)) {
            throw new JsonHttpException(400, 'Not Valid');
        }
        $checkList->addItem($item);
        $em = $this->getDoctrine()->getManager();
        $em->persist($checkList);
        $em->flush();

        return $this->json($item);
    }

    /**
     * @Route("/api/list/{checkList}/item/{item}", methods={"DELETE"})
     */
    public function removeAction(CheckList $checkList, Item $item)
    {
        $user = $this->getUser();
        $userLists = $user->getCheckLists();
        if (!$userLists->contains($checkList)) {
            throw new JsonHttpException(400, 'You are not the owner of this list');
        }
        $items = $checkList->getItems();
        if (!$items->contains($item)) {
            throw new JsonHttpException(400, 'This Item does not belong to this list');
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($item);
        $em->flush();

        return $this->json(null, 200);
    }

    /**
     * @Route("/api/list/{checkList}/item/{item}", methods={"PUT"})
     */
    public function updateAction(CheckList $checkList, Item $item)
    {
        $user = $this->getUser();
        $userLists = $user->getCheckLists();
        if (!$userLists->contains($checkList)) {
            throw new JsonHttpException(400, 'You are not the owner of this list');
        }
        $items = $checkList->getItems();
        if (!$items->contains($item)) {
            throw new JsonHttpException(400, 'This Item does not belong to this list');
        }
        if ($item->getChecked()) {
            $item->setChecked(false);
        } else {
            $item->setChecked(true);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($item);
        $em->flush();

        return $this->json($item);
    }
}
