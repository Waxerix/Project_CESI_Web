<?php
namespace App\Controllers;
use App\Models\CandidacyModel;
use App\Core\Controller;
use App\Models\JobOfferModel;
use App\Models\AccessModel;
class ApplyFormController extends Controller
{
    private $jobModel;
    public function __construct($twig, $pdo)
    {
        $this->pdo = $pdo;
        $this->twig = $twig;
        $this->Model = new CandidacyModel($pdo);
        $this->jobModel = new JobOfferModel($pdo);
        $this->access = new AccessModel($pdo);
    }

    public function storeCandidacy(int $offerId) : void
    {
        $access = new AccessModel($this->pdo);
        $user = $access->curentUser();

        $userId = $user['user_id'] ?? null;
        
        $comment = $_POST['comment'] ?? null;
        $LM = $_FILES['LM'] ?? null;
        $CV = $_FILES['CV'] ?? null;
        if ($this->Model->createCandidacy($userId, $offerId, $comment)) {
            $this->Model->storeFile($LM);
            $this->Model->storeFile($CV);
            header('Location: /');
            exit;
        } else {
            echo $this->twig->render('formulaire-postuler.html.twig', [
                'id' => $offerId,
                'erreur' => 'Une erreur est survenue lors de la soumission de votre candidature.',
            ]);
        }
    }

    public function printApplyForm(int $offerId): void
    {
        
        $user = $this->access->curentUser();
        $offre=$this->jobModel->getOfferById($offerId);
        echo $this->twig->render('formulaire-postuler.html.twig', ['offre' => $offre, 'user' => $user['pseudo']]);
    }
}