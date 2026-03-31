<?php
namespace App\Controllers;
use App\Models\CandidacyModel;
use App\Core\Controller;
use App\Models\JobOfferModel;
class ApplyFormController extends Controller
{
    private $jobModel;
    public function __construct($twig, $pdo)
    {
        $this->twig = $twig;
        $this->Model = new CandidacyModel($pdo);
        $this->jobModel = new JobOfferModel($pdo);
    }

    public function storeCandidacy(): void
    {
        $userId = $_SESSION['user_id'] ?? null;
        $offerId = $_POST['offer_id'] ?? null;
        $comment = $_POST['comment'] ?? null;
        if ($this->Model->createCandidacy($userId, $offerId, $comment)) {
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
        $offre=$this->jobModel->getOfferById($offerId);
        echo $this->twig->render('formulaire-postuler.html.twig', ['offre' => $offre]);
    }
}