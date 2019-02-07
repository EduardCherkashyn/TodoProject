<?php
/**
 * Created by PhpStorm.
 * User: eduardcherkashyn
 * Date: 1/18/19
 * Time: 18:51
 */

namespace App\Controller\Api;

use App\Entity\CheckList;
use App\Entity\Label;
use App\Exception\JsonHttpException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LabelController extends AbstractController
{
    /**
     * @Route("/api/label", methods={"POST"})
     */
    public function createAction(Request $request, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        if (!$content = $request->getContent()) {
            throw new JsonHttpException(400, 'Bad Request');
        }
        $label = $serializer->deserialize($content, Label::class, 'json');
        $errors = $validator->validate($label);
        if (count($errors)) {
            throw new JsonHttpException(400, 'Bad Request');
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($label);
        $em->flush();

        return ($this->json($label));
    }

    /**
     * @Route("/api/label/{id}", methods={"DELETE"})
     */
    public function deleteAction(Label $label)
    {
        $user = $this->getUser();
        if (in_array("ROLE_ADMIN", $user->getRoles())) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($label);
            $em->flush();

            return new JsonResponse(null, 200);
        }

        throw new JsonHttpException(400, 'Bad Request');
    }

    /**
     * @Route("/api/label/{id}", methods={"PUT"})
     */
    public function editAction(Label $label, Request $request)
    {
        if (!$content = $request->getContent()) {
            throw new JsonHttpException(400, 'Bad Request');
        }
        $user = $this->getUser();
        if (in_array("ROLE_ADMIN", $user->getRoles())) {
            $data = json_decode($content, true);
            $label->setName($data['name']);
            $em = $this->getDoctrine()->getManager();
            $em->persist($label);
            $em->flush();

            return ($this->json($label));
        }

        throw new JsonHttpException(400, 'Bad Request');
    }

    /**
     * @Route("/api/label/{label}/checklist/{checkList}", methods={"POST"})
     */
    public function addCheckListAction(Label $label, CheckList $checkList)
    {
        if ($label->getChecklists()->contains($checkList)) {
            throw new JsonHttpException(400, 'Bad Request');
        }
        $label->addChecklist($checkList);
        $em = $this->getDoctrine()->getManager();
        $em->persist($label);
        $em->flush();

        return ($this->json($label));
    }

    /**
     * @Route("/api/label/{label}/checklist/{checkList}", methods={"DELETE"})
     */
    public function removeCheckListAction(Label $label, CheckList $checkList)
    {
        if ($label->getChecklists()->contains($checkList)) {
            $label->removeChecklist($checkList);
            $em = $this->getDoctrine()->getManager();
            $em->persist($label);
            $em->flush();
        }
        return ($this->json($label));
    }
}
