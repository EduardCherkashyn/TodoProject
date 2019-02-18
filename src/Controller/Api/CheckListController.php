<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 1/18/19
 * Time: 13:41
 */

namespace App\Controller\Api;

use App\Entity\CheckList;
use App\Entity\User;
use App\Exception\JsonHttpException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CheckListController extends AbstractController
{
    /**
     * @Route("/api/checklist", methods={"GET"})
     */
    public function getAllAction()
    {
        $user = $this->getUser();

        return ($this->json($user->getCheckLists()));
    }

    /**
     * @Route("/api/checklist", methods={"POST"})
     */
    public function createAction(Request $request, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        if (!$content = $request->getContent()) {
            throw new JsonHttpException(400, 'Bad Request');
        }
        $user = $this->getUser();
        $data = json_decode($content, true);
        try {
            new \DateTime($data['expire']);
        } catch (Exception $e) {
            throw new JsonHttpException(400, 'Not Valid');
        }
        $checklist = $serializer->deserialize($content, CheckList::class, 'json');
        $errors = $validator->validate($checklist);
        if (count($errors)) {
            throw new JsonHttpException(400, 'Not Valid');
        }
        $user->addCheckList($checklist);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $this->json($checklist);
    }

    /**
     * @Route("/api/checklist/{id}", methods={"DELETE"})
     */
    public function deleteAction(CheckList $checkList)
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $checkLists = $user->getCheckLists();
        if (!$checkLists->contains($checkList)) {
            throw new JsonHttpException(404, 'You are not the owner of current resource');
        }
        $em = $this->getDoctrine()->getManager();
        $user->removeCheckList($checkList);
        $em->persist($user);
        $em->flush();

        return $this->json(null,200);
    }

    /**
     * @Route("/api/checklist/{id}", methods={"PUT"})
     */
    public function editAction(CheckList $checkList, Request $request, ValidatorInterface $validator)
    {
        if (!$content = $request->getContent()) {
            throw new JsonHttpException(400, 'Bad Request');
        }
        $user = $this->getUser();
        $userLists = $user->getCheckLists();
        if (!$userLists->contains($checkList)) {
            throw new JsonHttpException(400, 'You are not the owner of current resource');
        }
        $data = json_decode($content, true);
        try {
           $date =  new \DateTime($data['expire']);
        } catch (Exception $e) {
            throw new JsonHttpException(400, 'Not Valid date');
        }
        $checkList->setName($data['name']);
        $checkList->setExpire($date);
        $errors = $validator->validate($checkList);
        if (count($errors)) {
            throw new JsonHttpException(400, 'Not Valid');
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($checkList);
        $em->flush();

        return $this->json($checkList);
    }
}
