<?php

namespace Renovate\MainBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Renovate\MainBundle\Entity\Job;
use Renovate\MainBundle\Entity\Document;

class JobsController extends Controller
{
    public function indexAction()
    {
    	$timestamp = time();
    	$token = Document::getToken($timestamp);
    	return $this->render('RenovateMainBundle:Jobs:index.html.twig', array(
    			'timestamp' => $timestamp,
    			'token' => $token
    	));
    }
    
    public function showJobAction($job_name_translit)
    {
    	$em = $this->getDoctrine()->getManager();
    	$job = $em->getRepository("RenovateMainBundle:Job")->findByNameTranslit($job_name_translit);
    	
    	return $this->render('RenovateMainBundle:Jobs:showJob.html.twig', array("job" => $job[0]));
    }
    
    public function getJobsNgAction(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();
    	
    	$response = new Response(json_encode(array("result" => Job::getJobs($em, $request->query->all(), true))));
    	$response->headers->set('Content-Type', 'application/json');
    	 
    	return $response;
    }
    
    public function getJobsCountNgAction()
    {
    	$em = $this->getDoctrine()->getManager();
    	 
    	$response = new Response(json_encode(array("result" => Job::getJobsCount($em))));
    	$response->headers->set('Content-Type', 'application/json');
    	
    	return $response;
    }
    
    
    public function addJobNgAction()
    {
    	$em = $this->getDoctrine()->getManager();
    	$data = json_decode(file_get_contents("php://input"));
    	$parameters = (object) $data;
    	
    	$transliterater = $this->get('renovate.transliterater');
    	$job = Job::addJob($em, $transliterater, $this->getUser(), $parameters);
    	
    	$response = new Response(json_encode(array("result" => $job->getInArray())));
    	$response->headers->set('Content-Type', 'application/json');
    	 
    	return $response;
    }
    
    
    public function removeJobNgAction($job_id)
    {
    	$em = $this->getDoctrine()->getManager();
    	
    	$response = new Response(json_encode(array("result" => Job::removeJobById($em, $job_id))));
    	$response->headers->set('Content-Type', 'application/json');
    	
    	return $response;
    }
    
    
    public function editJobNgAction($job_id)
    {
    	$em = $this->getDoctrine()->getManager();
    	$data = json_decode(file_get_contents("php://input"));
    	$parameters = (object) $data;
    	
    	$transliterater = $this->get('renovate.transliterater');
    	$job = Job::editJobById($em, $transliterater, $job_id, $parameters);
    	
    	$response = new Response(json_encode(array("result" => $job->getInArray())));
    	$response->headers->set('Content-Type', 'application/json');
    	 
    	return $response;
    }
}
