<?php

namespace App\Controller;

use App\Asterisk\Entity\Extens;
use App\Entity\BreakGroup;
use App\Entity\Group;
use App\Entity\Team;
use App\Entity\User;
use App\Entity\UserLog;
use App\Entity\UserProfile;
use App\Form\UserCategoryType;
use App\Form\UserNoneLdapEditType;
use App\Form\UserNoneLdapType;
use App\Form\UserProfileType;
use App\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\Choice;
use function MongoDB\BSON\toJSON;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @IsGranted("ROLE_USER_INDEX")
     * @Route("/", name="user_index", methods="GET")
     */
    public function index(): Response
    {
        $users = $this->getDoctrine()
            ->getRepository(User::class)
            ->findAll();

        return $this->render('user/index.html.twig', ['users' => $users]);
    }

    public function toJson($data)
    {
        return $this->container->get('serializer')->serialize($data, 'json');
    }

    /**
     * @IsGranted("ROLE_USER_NEW")
     * @Route("/new", name="user_new", methods="GET|POST")
     * @param Request $request
     * @param UserInterface $changedUser
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     * @throws \Exception
     */
    public function new(Request $request, UserInterface $changedUser, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();

        $form3 = $this->createForm(UserCategoryType::class, $user);
        $form3->handleRequest($request);

        if ($form3->isSubmitted() && $form3->isValid()){

            $userProfile = new UserProfile();
            if ($user->getUserCategory()->getId() == 1){
                $form = $this->createForm(UserType::class, $user);
            }else{
                $form = $this->createForm(UserNoneLdapType::class, $user);
            }
            //$form_2 = $this->createForm(UserProfileType::class, $userProfile);

            $notExtension = [9341001000, 9341001900, 9341001901, 9341001902, 9341001903, 9341001904, 9341001905, 9341001906, 9341001907, 9341001908, 9341001909, 9341001910, 9341001911, 9341001912, 9341001913, 9341001914, 9341001915, 9341001916, 9341001917, 9341001918, 9341001919, 9341001920, 9341001921, 9341001922, 9341001923, 9341001924, 9341001925, 9341001926, 9341001927, 9341001928, 9341001929, 9341001930, 9341001931, 9341001932, 9341001933, 9341001934, 9341001935, 9341001936, 9341001937, 9341001938, 9341001939, 9341001940, 9341001941, 9341001942, 9341001943, 9341001944, 9341001945, 9341001946, 9341001947, 9341001948, 9341001949, 9341001950, 9341001951, 9341001952, 9341001953, 9341001954, 9341001955, 9341001956, 9341001957, 9341001958, 9341001959, 9341001960, 9341001961, 9341001962, 9341001963, 9341001964, 9341001965, 9341001966, 9341001967, 9341001968, 9341001969, 9341001970, 9341001971, 9341001972, 9341001973, 9341001974, 9341001975, 9341001976, 9341001977, 9341001978, 9341001979, 9341001980, 9341001981, 9341001982, 9341001983, 9341001984, 9341001985, 9341001986, 9341001987, 9341001988, 9341001989, 9341001990, 9341001991, 9341001992, 9341001993, 9341001994, 9341001995, 9341001996, 9341001997, 9341001998, 9341001999,];
            $extensionRepo = $this->getDoctrine()->getRepository(Extens::class);
            $extensions = $extensionRepo->createQueryBuilder("ex");
            $extensions
                ->select('ex.exten')
                ->where(
                    $extensions->expr()->notIn("ex.exten",$notExtension)
                );
            $extensions = $extensions->getQuery()->getArrayResult();

            $usedExtensions = $this->getDoctrine()->getRepository(UserProfile::class)->createQueryBuilder("up")
                ->select('up.extension')
                ->getQuery()->getArrayResult();
            $arr = [];
            foreach ($extensions as $ee) {
                foreach ($ee as $key => $value) {
                    $arr [$value] = $value;
                }
            }
            $arr2 = [];
            foreach ($usedExtensions as $ee) {
                foreach ($ee as $key => $value) {
                    $arr2 [$value] = $value;
                }
            }
            $extens = array("Seçiniz" => "0");
            $extens = $extens + array_diff($arr, $arr2);


            $qfb = $this->createFormBuilder($userProfile);
            $qfb
                ->add('tckn', IntegerType::class, [
                    "label" => "Tc Kimlik No"])
                ->add('firstName', null, [
                    "label" => "İsim"])
                ->add('lastName', null, [
                    "label" => "Soy İsim"])
                ->add('extension', ChoiceType::class, [
                    "label" => "Dahili No",
                    "choices" => $extens,
                ]);

            $form_2 = $qfb->getForm();
            $form->handleRequest($request);
            $form_2->handleRequest($request);


            if ($form->isSubmitted() && $form->isValid() && $form_2->isSubmitted() && $form_2->isValid()) {
                if ($user->getUserCategory()->getId() == 1){
                    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
                    $pass = array(); //remember to declare $pass as an array
                    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
                    for ($i = 0; $i < 8; $i++) {
                        $n = rand(0, $alphaLength);
                        $pass[] = $alphabet[$n];
                    }

                    $usePass = $encoder->encodePassword($user,implode($pass));
                }else{
                    $usePass = $encoder->encodePassword($user,$user->getPassword());
                }

                $form->getViewData()->setPassword($usePass);
                $form->getViewData()->setEnabled(1);
                $em = $this->getDoctrine()->getManager();
                $user->setState(0);
                $user->setLastStateChange(new \DateTime());
                $em->persist($user);
                $em->flush();
                $form_2->getViewData()->setUser($user);
                $em->persist($userProfile);
                $em->flush();

                $userLogData = [
                    "user" => $user,
                    "userProfile" => $userProfile,
                ];

                $tojson = $this->toJSON($userLogData);

                $userLog = new UserLog();

                /**
                 * @var User $changedUser
                 */
                $userLog
                    ->setCreatedAt(new \DateTime())
                    ->setUpdatedAt(new \DateTime())
                    ->setChangedUser($changedUser)
                    ->setChangeUser($user)
                    ->setOldchangeUser($tojson)
                    ->setNewChangeUser($tojson);

                $em->persist($userLog);
                $em->flush();

                return $this->redirectToRoute('user_index');
            }

            return $this->render('user/new.html.twig', [
                'user' => $user,
                'form' => $form->createView(),
                'form_2' => $form_2->createView(),
                'form_3' => $form3->createView(),
            ]);
        }

        return $this->render('user/new_category_select.html.twig', [
            'user' => $user,
            'form' => $form3->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_USER_SHOW")
     * @Route("/{id}", name="user_show", methods="GET")
     */
    public function show(User $user): Response
    {
        $em = $this->getDoctrine()->getManager();
        $userProfile = $em->getRepository(UserProfile::class)->findBy(["user" => $user]);
        return $this->render('user/show.html.twig', ['user' => $user, 'userProfile' => $userProfile[0]]);
    }

    /**
     * @IsGranted("ROLE_USER_EDIT")
     * @Route("/{id}/edit", name="user_edit", methods="GET|POST")
     * @param Request $request
     * @param User $user
     * @param UserInterface $changedUser
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function edit(Request $request, User $user, UserInterface $changedUser, UserPasswordEncoderInterface $encoder): Response
    {
        if ($user->getUserCategory()->getId() == 1){
            $form = $this->createForm(UserType::class, $user);
        }else{
            $form = $this->createForm(UserNoneLdapEditType::class, $user);
        }
        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $userProfile = $em->getRepository(UserProfile::class)->findBy(["user" => $user]);

        //$form_2 = $this->createForm(UserProfileType::class, $userProfile[0]);
        $extensionRepo = $this->getDoctrine()->getRepository(Extens::class);
        $extensions = $extensionRepo->createQueryBuilder("ex");
        $extensions
            ->select('ex.exten')
            ->where(
               "ex.tech = 'PJSIP' "
            );
        $extensions = $extensions->getQuery()->getArrayResult();

        $usedExtensions = $this->getDoctrine()->getRepository(UserProfile::class)->createQueryBuilder("up")
            ->select('up.extension')
            ->getQuery()->getArrayResult();

        $arr = [];
        foreach ($extensions as $ee) {
            foreach ($ee as $key => $value) {
                $arr [$value] = $value;
            }
        }
        $arr2 = [];
        foreach ($usedExtensions as $ee) {
            foreach ($ee as $key => $value) {
                $arr2 [$value] = $value;
            }
        }
        $extens = array($userProfile[0]->getExtension() => $userProfile[0]->getExtension());
        $extens = $extens + array("Seçiniz" => "0");
        $extens = $extens + array_diff($arr, $arr2);


        $qfb = $this->createFormBuilder($userProfile[0]);
        $qfb
            ->add('tckn', IntegerType::class, [
                "label" => "Tc Kimlik No"])
            ->add('firstName', null, [
                "label" => "İsim"])
            ->add('lastName', null, [
                "label" => "Soy İsim"])
            ->add('extension', ChoiceType::class, [
                "label" => "Dahili",
                "choices" => $extens,
            ]);

        $form_2 = $qfb->getForm();
        $form_2->handleRequest($request);

        $oldUserLogData = [
            "user" => $user,
            "userProfile" => $userProfile,
        ];
        $oldTojson = $this->toJSON($oldUserLogData);

        $userLog = new UserLog();
        $userLog
            ->setOldchangeUser($oldTojson)
            ->setChangeUser($user);
        $userLogForm = $this->createFormBuilder($userLog);
        $userLogForm
            ->add("oldChangeUser")
            ->add("changeUser");
        $form_3 = $userLogForm->getForm();
        $form_3->handleRequest($request);

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'form_2' => $form_2->createView(),
            'form_3' => $form_3->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_USER_EDIT")
     * @Route("/user-update-save", name="user_update_save")
     * @param Request $request
     * @param UserInterface $changedUser
     * @param UserPasswordEncoderInterface $encoder
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function updateSave(Request $request, UserInterface $changedUser, UserPasswordEncoderInterface $encoder)
    {
        $postData = $request->request->all();


        $em = $this->getDoctrine()->getManager();
        $user = $em->find(User::class, $postData["form"]["changeUser"]);
        if ($user->getUserCategory()->getId() == 1){
            $postDataFormUser = $postData["user"];
        }else{
            $postDataFormUser = $postData["user_none_ldap_edit"];
        }

        $formGroups = $postDataFormUser["groups"];
        $groups = $em->getRepository(Group::class)->findAll();
        foreach ($groups as $group) {
            $user->removeGroup($group);
        }
        $em->persist($user);
        $em->flush();
        foreach ($formGroups as $formGroup) {
            $user
                ->setUpdatedAt(new \DateTime())
                ->setUsername($postDataFormUser["username"])
                ->setEmail($postDataFormUser["email"])
                ->setBreakGroup($em->find(BreakGroup::class, $postDataFormUser["breakGroup"]))
                ->setTeamId($em->find(Team::class, $postDataFormUser["teamId"]))
                ->addGroup($em->find(Group::class, $formGroup));
        }

        if (array_key_exists("password",$postDataFormUser)){
            if (array_search($postDataFormUser["password"],["undefined",null,""," "]) == false){
                $userPass = $encoder->encodePassword($user,$postDataFormUser["password"]);
                $user->setPassword($userPass);
            }
        }

        $em->persist($user);
        $em->flush();
        /**
         * @var UserProfile $userProfile
         */
        $userProfile = $user->getUserProfile();
        $userProfile
            ->setTckn($postData["form"]["tckn"])
            ->setFirstName($postData["form"]["firstName"])
            ->setLastName($postData["form"]["lastName"])
            ->setExtension($postData["form"]["extension"]);

        $em->persist($userProfile);
        $em->flush();

        $newUserLogData = [
            "user" => $user,
            "userProfile" => $userProfile,
        ];

        $newTojson = $this->toJSON($newUserLogData);

        $userLog = new UserLog();

        /**
         * @var User $changedUser
         */
        $userLog
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime())
            ->setChangedUser($changedUser)
            ->setChangeUser($user)
            ->setOldchangeUser($postData["form"]["oldChangeUser"])
            ->setNewChangeUser($newTojson);

        $em->persist($userLog);
        $em->flush();

        return $this->redirectToRoute('user_index');
    }

    /**
     * @IsGranted("ROLE_BHM_TEKNOLOJI")
     * @Route("/change-password/{user}/{password}", name="change_password_user", methods={"GET"})
     * @param User $user
     * @param UserPasswordEncoderInterface $encoder
     * @param $password
     * @return Response
     * @throws \Exception
     */
    public function changePasswordUser(User $user, UserPasswordEncoderInterface $encoder, $password)
    {
        $em = $this->getDoctrine()->getManager();
        $newPassword = $encoder->encodePassword($user, $password);
        $user->setUpdatedAt(new \DateTime())->setPassword($newPassword);
        $em->persist($user);
        $em->flush();

        return new Response("Şifre Başarıyla Değiştirildi");
    }
}
