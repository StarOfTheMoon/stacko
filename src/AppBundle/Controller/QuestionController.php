<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Question;
use AppBundle\Entity\Reponse;
use AppBundle\Form\ReponseType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Knp\Component\Pager\Paginator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Question controller.
 *
 * @Route("question")
 */
class QuestionController extends Controller
{
    /**
     * Lists all question entities.
     *
     * @Route("/", name="question_index")
     * @Method("GET")
     */
    public function indexAction(Paginator $paginator, ObjectManager $em, $page = 1, Request $request)
    {
        $query = $em->createQuery('SELECT li FROM AppBundle:Question li ORDER BY li.title ASC');

       $pagination = $paginator->paginate($query,$page);

       return $this->render('question/index.html.twig', [
            'questions' => $pagination
        ]);
    }
    /**
     * Creates a new question entity.
     *
     * @Route("/new", name="question_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $question = new Question();
        $form = $this->createForm('AppBundle\Form\QuestionType', $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($question);
            $em->flush();

            return $this->redirectToRoute('question_show', array('id' => $question->getId()));
        }

        return $this->render('question/new.html.twig', array(
            'question' => $question,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a question entity.
     *
     * @Route("/{id}", name="question_show")
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("question", class="AppBundle:Question")
     */
    public function showAction(Question $question, Request $request, ObjectManager $em)
    {
        $deleteForm = $this->createDeleteForm($question);

        $form = $this->createForm(ReponseType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $reponse = $form->getData();
            $reponse->setUser($this->getUser());
            $reponse->setQuestion($question);
            $em->persist($reponse);
            $em->flush();

            $this->addFlash(
                'notice',
                'Votre réponse a été ajoutée. Merci.'
            );

            return $this->redirectToRoute('question_show', [
                'id'=>$question->getId()
            ]);
        }
 
        return $this->render('question/show.html.twig', array(
            'question' => $question,
            'form' => $form->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
      }

    /**
     * Displays a form to edit an existing question entity.
     *
     * @Route("/{id}/edit", name="question_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Question $question)
    {
        $deleteForm = $this->createDeleteForm($question);
        $editForm = $this->createForm('AppBundle\Form\QuestionType', $question);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('question_edit', array('id' => $question->getId()));
        }

        return $this->render('question/edit.html.twig', array(
            'question' => $question,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a question entity.
     *
     * @Route("/{id}", name="question_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Question $question)
    {
        $form = $this->createDeleteForm($question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($question);
            $em->flush();
        }

        return $this->redirectToRoute('question_index');
    }

    /**
     * Creates a form to delete a question entity.
     *
     * @param Question $question The question entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Question $question)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('question_delete', array('id' => $question->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Add a vote to a reponse entity.
     *
     * @Route("/reponse/{id}/vote/{vote}", name="reponse_vote", requirements={"vote": "▲|▼"})
     * @Security("has_role('ROLE_USER')")
     * @ParamConverter("reponse", class="AppBundle:Reponse")
     */
    public function voteAction(Reponse $reponse, $vote, ObjectManager $em)
    {
        $current_vote = $reponse->getVote();
        $new_vote = $vote == "▲" ?$current_vote : --$current_vote ;
        $reponse->setVote($new_vote);
        $em->persist($reponse);
        $em->flush();
        return $this->redirectToRoute('question_show', [
            'id' => $reponse->getQuestion()->getId()
        ]);

    }
}
