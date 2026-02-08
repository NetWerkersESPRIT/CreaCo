<?php

namespace App\Controller;

use App\Entity\Contract;
use App\Repository\ContractRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/contract/sign')]
class PublicContractController extends AbstractController
{
    #[Route('/{contractNumber}/{token}', name: 'app_public_contract_signature_view', methods: ['GET'])]
    public function viewSignature(string $contractNumber, string $token, ContractRepository $repo): Response
    {
        $contract = $repo->findOneBy([
            'contractNumber' => $contractNumber,
            'signatureToken' => $token
        ]);

        if (!$contract) {
            throw $this->createNotFoundException('Contrat introuvable ou lien invalide.');
        }

        return $this->render('public/contract/signature.html.twig', [
            'contract' => $contract,
        ]);
    }

    #[Route('/{contractNumber}/{token}/accept', name: 'app_public_contract_signature_accept', methods: ['POST'])]
    public function acceptSignature(string $contractNumber, string $token, Request $request, EntityManagerInterface $em, ContractRepository $repo): Response
    {
        $contract = $repo->findOneBy([
            'contractNumber' => $contractNumber,
            'signatureToken' => $token
        ]);

        if (!$contract) {
            throw $this->createNotFoundException('Lien invalide.');
        }

        $fullName = $request->request->get('full_name');
        if (empty($fullName)) {
            $this->addFlash('error', 'Le nom complet est obligatoire pour la signature.');
            return $this->redirectToRoute('app_public_contract_signature_view', [
                'contractNumber' => $contractNumber,
                'token' => $token
            ]);
        }

        $contract->setSignedByCollaborator(true);
        $contract->setCollaboratorSignatureDate(new \DateTime());
        $contract->setStatus('SIGNED_BY_COLLABORATOR');

        // On pourrait stocker le nom du signataire dans un champ dédié si nécessaire, 
        // ici on valide simplement l'action demandée.

        $em->flush();

        return $this->render('public/contract/signature_success.html.twig', [
            'contract' => $contract,
        ]);
    }

    #[Route('/{contractNumber}/{token}/reject', name: 'app_public_contract_signature_reject', methods: ['POST'])]
    public function rejectSignature(string $contractNumber, string $token, Request $request, EntityManagerInterface $em, ContractRepository $repo): Response
    {
        $contract = $repo->findOneBy([
            'contractNumber' => $contractNumber,
            'signatureToken' => $token
        ]);

        if (!$contract) {
            throw $this->createNotFoundException('Lien invalide.');
        }

        $reason = $request->request->get('rejection_reason');

        $contract->setStatus('REJECTED');
        if (!empty($reason)) {
            $contract->setCancellationTerms($reason);
        }

        $collabRequest = $contract->getCollabRequest();
        if ($collabRequest) {
            $collabRequest->setStatus('REJECTED');
            $collabRequest->setRejectionReason('Refus collaborateur : ' . ($reason ?? 'Pas de motif fourni'));
            $collabRequest->setRespondedAt(new \DateTime());
        }

        $em->flush();

        return $this->render('public/contract/rejection_success.html.twig', [
            'contract' => $contract,
        ]);
    }
}
