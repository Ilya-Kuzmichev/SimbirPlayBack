<?php

namespace actions\admin;

use actions\Action;
use helpers\Image;
use helpers\ReturnedResponse;
use models\Achievement;
use models\AchievementToChallenge;
use models\Challenge;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Validator;

class ChallengeAction extends Action
{
    public function create(Request $request, Response $response, $args)
    {
        $returnResponse = new ReturnedResponse($response);
        $attributes = [
            'name' => $request->getParam('name'),
            'description' => $request->getParam('description'),
            'start_date' => $request->getParam('startDate'),
            'end_date' => $request->getParam('endDate'),
            'budget' => $request->getParam('budget'),
            'responsible_id' => $request->getParam('responsible'),
        ];
        if ($imageBase64 = $request->getParam('image')) {
            if ($picture = (new Image())->base64ToImage($imageBase64, $this->container->uploadDir . 'challenge/')) {
                $attributes['image'] = $picture;
            }
        }
        $challenge = new Challenge();
        if ($errors = $this->validator->validate($attributes, [
            'name' => Validator::notEmpty()->stringType()->length(1, 255),
            'description' => Validator::stringType()->length(0, 10000),
            'start_date' => Validator::noWhitespace()->date(),
            'end_date' => Validator::noWhitespace()->date(),
            'budget' => Validator::noWhitespace()->intVal()->between(0, 10000),
            'responsible_id' => Validator::noWhitespace()->intVal(),
        ])) {
            return $returnResponse->errorsResponse($errors);
        }
        $challenge->fill($attributes);
        if ($challenge->save()) {
            $achievementIds = $request->getParam('achievementIds', []);
            foreach ($achievementIds as $achievementId) {
                $achievement = $this->db->table((new Achievement())->getTable())
                    ->get('id')->where('id', $achievementId)->shift();
                if ($achievement) {
                    $achievementToChallenge = new AchievementToChallenge();
                    $achievementToChallenge->fill([
                        'challenge_id' => $challenge->id,
                        'achievement_id' => $achievementId,
                    ]);
                    $achievementToChallenge->save();
                }
            }
            return $returnResponse->successResponse([
                'id' => $challenge->id,
            ]);
        }
        return $returnResponse->saveErrorResponse();
    }
}