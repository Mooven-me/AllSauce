<?php

namespace App\Dto\Auth;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class AuthCredentialsDto
{
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Assert\Length(max: 180)]
    public string $email;

    public function __construct(
        string $email,

        #[Assert\NotBlank]
        #[Assert\Length(min: 6, max: 4096)]
        public string $password,
    ) {
        $this->email = mb_strtolower(trim($email));
    }
}
