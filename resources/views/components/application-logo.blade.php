{{--
    Marca do InamexControl: coracao (acolhimento) com uma flor (cuidado/
    esperanca), nas cores da identidade visual do instituto. Espaco reservado
    para substituir por <img src="{{ asset('images/inamex-logo.png') }}">
    quando o arquivo oficial do logo (hand + heart + flower) estiver disponivel.
--}}
<svg viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg" {{ $attributes }}>
    <path
        d="M60 102C45 90 18 70 18 46C18 30 30 18 45 18C52 18 58 21.5 60 27C62 21.5 68 18 75 18C90 18 102 30 102 46C102 70 75 90 60 102Z"
        fill="currentColor"
        class="text-brand-600"
    />
    <g transform="translate(60 10)">
        <circle cx="0" cy="-6" r="6" class="text-accent-500" fill="currentColor" />
        <circle cx="8" cy="-1" r="6" class="text-accent-500" fill="currentColor" />
        <circle cx="5" cy="8" r="6" class="text-accent-500" fill="currentColor" />
        <circle cx="-5" cy="8" r="6" class="text-accent-500" fill="currentColor" />
        <circle cx="-8" cy="-1" r="6" class="text-accent-500" fill="currentColor" />
        <circle cx="0" cy="1" r="5" class="text-accent-700" fill="currentColor" />
    </g>
</svg>
