<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Answer;
use App\Entity\Progress;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\Result;
use App\Entity\Role;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    const USER_COUNT = 50;
    const ADMIN_COUNT = 2;
    const QUIZ_COUNT = 100;
    const ANSWERS_PER_QUESTION_COUNT = 4;
    const QUESTIONS_PER_QUIZ_COUNT = 10;
    const QUESTIONS_COUNT = self::QUIZ_COUNT * self::QUESTIONS_PER_QUIZ_COUNT;

    private UserPasswordEncoderInterface $encoder;

    /**
     * AppFixtures constructor.
     * @param $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $users = [];
        $questions = [];

        $roleUser = new Role();
        $roleUser->setName('ROLE_USER');
        $roleAdmin = new Role();
        $roleAdmin->setName('ROLE_ADMIN');
        $manager->persist($roleUser);
        $manager->persist($roleAdmin);

        for ($i = 0; $i < self::USER_COUNT; $i++) {
            $user = new User();
            $user->setName('UserNo' . $i)
                ->setEmail('mail' . $i . '@test.tu')
                ->setPassword($this->encoder->encodePassword($user, 'qwe123'))
                ->addRole($roleUser)
                ->setIsActive(true)
                ->setIsVerified(true);
            $manager->persist($user);
            array_push($users, $user);
        }

        for ($i = 0; $i < self::ADMIN_COUNT; $i++) {
            $admin = new User();
            $admin->setName('AdminNo' . $i)
                ->setEmail('adminmail' . $i . '@test.tu')
                ->setPassword($this->encoder->encodePassword($admin, 'qwe123'))
                ->addRole($roleAdmin)
                ->setIsActive(true)
                ->setIsVerified(true);
            $manager->persist($admin);
            array_push($users, $admin);
        }

        for ($i = 0; $i < self::QUESTIONS_COUNT; $i++) {
            $question = new Question();
            $question->setName('questionNo' . $i);
            for ($k = 0; $k < self::ANSWERS_PER_QUESTION_COUNT; $k++) {
                $answer = new Answer();
                $answer->setQuestion($question)
                    ->setName('answerNo' . $i . ':' . $k)
                    ->setIsRight($k === 0);
                $question->addAnswer($answer);
                $manager->persist($answer);
            }
            $manager->persist($question);
            array_push($questions, $question);
        }

        for ($i = 0; $i < self::QUIZ_COUNT; $i++) {
            $quiz = new Quiz();
            $quiz->setName('quizNo' . $i)
                ->setIsActive((bool)rand(0, 5))
                ->setCreateDate((new DateTime())->setTimestamp(strtotime(sprintf("-%d days", rand(1, self::QUIZ_COUNT)))));
            for ($k = 0; $k < self::QUESTIONS_PER_QUIZ_COUNT; $k++) {
                $quiz->addQuestion($questions[rand(0, self::QUESTIONS_COUNT - 1)]);
            }
            $participantCount = rand(5, 15);
            $usersCopy = $users;
            shuffle($usersCopy);
            for ($p = 0; $p < $participantCount; $p++) {
                $result = new Result();

                /** @var User $user */
                $user = array_pop($usersCopy);
                $user->addResult($result);
                $quiz->addResult($result);

                $result
                    ->setStartDate((new DateTime())->setTimestamp(strtotime(sprintf("-%d hours", rand(1, self::QUIZ_COUNT)))))
                    ->setEndDate(rand(0, 5) ? new DateTime() : null);

                $progressCount = $result->getEndDate() ? self::QUESTIONS_PER_QUIZ_COUNT : rand(0, self::QUESTIONS_PER_QUIZ_COUNT);
                $rightAnswersCount = 0;
                for ($j = 0; $j < $progressCount; $j++) {
                    $progress = (new Progress())
                        ->setQuestion($questions[$i*$j+$j])
                        ->setIsRight(random_int(0, 10) >= 3 ? false : true);
                    $result->addProgress($progress);
                    $manager->persist($progress);
                    if ($progress->getIsRight()) {
                        $rightAnswersCount++;
                    }
                }
                $result->setResult($rightAnswersCount);
                $manager->persist($result);
            }
            $manager->persist($quiz);
        }
        $manager->flush();
    }
}
