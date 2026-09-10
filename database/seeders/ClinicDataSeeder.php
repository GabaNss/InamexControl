<?php

namespace Database\Seeders;

use App\Models\FichaMedica;
use App\Models\Paciente;
use App\Models\ProntuarioDiario;
use App\Models\ProntuarioHistorico;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Popula fichas_medicas, prontuarios_diarios e prontuarios_historicos
 * com dados clínicos de exemplo em português.
 *
 * Uso: php artisan db:seed --class=ClinicDataSeeder
 */
class ClinicDataSeeder extends Seeder
{
    public function run(): void
    {
        $medico     = 101; // Demo Medico 2
        $enfermeiro = 103; // Demo Enfermeiro 4

        $pacientes = Paciente::all();

        $fichas = [
            [
                'diagnosticos'        => "F20.0 – Esquizofrenia paranoide\nHipótese secundária: Transtorno de personalidade esquizotípico (F21)",
                'medicamentos_cronicos' => "Risperidona 3mg – 1 comprimido à noite\nBiperiden 2mg – 1 comprimido ao almoço (para controle de EPS)",
                'laudos'              => "ECG 10/06/2025: Ritmo sinusal, FC 72 bpm, sem alterações.\nHemograma 12/06/2025: Dentro dos parâmetros de referência.\nProlactina 12/06/2025: 42 ng/mL (levemente elevada, monitorar).",
            ],
            [
                'diagnosticos'        => "F31.1 – Transtorno afetivo bipolar, episódio atual maníaco sem sintomas psicóticos",
                'medicamentos_cronicos' => "Carbonato de Lítio 300mg – 2 comprimidos 12/12h\nÁcido Valpróico 500mg – 1 comprimido à noite",
                'laudos'              => "Lítio sérico 08/07/2025: 0,8 mEq/L (nível terapêutico).\nTSH 08/07/2025: 2,1 µUI/mL (normal).\nFunção renal: Creatinina 0,9 mg/dL.",
            ],
            [
                'diagnosticos'        => "F32.2 – Episódio depressivo grave sem sintomas psicóticos\nComorbidade: F41.1 – Transtorno de ansiedade generalizada",
                'medicamentos_cronicos' => "Sertralina 100mg – 1 comprimido pela manhã\nClonazepam 0,5mg – 1 comprimido à noite (prazo limitado)",
                'laudos'              => "Escala Hamilton de Depressão 01/07/2025: Escore 24 (depressão grave).\nGlicemia em jejum: 94 mg/dL. Colesterol total: 178 mg/dL.",
            ],
            [
                'diagnosticos'        => "F60.3 – Transtorno de personalidade emocionalmente instável (tipo borderline)",
                'medicamentos_cronicos' => "Quetiapina 25mg – 1 comprimido à noite (uso adjunto)\nFluoxetina 20mg – 1 comprimido pela manhã",
                'laudos'              => "Avaliação neuropsicológica 20/06/2025: Déficit de regulação emocional compatível com diagnóstico. Recomendada psicoterapia DBT.\nSem achados neurológicos focais.",
            ],
            [
                'diagnosticos'        => "F25.0 – Transtorno esquizoafetivo, tipo maníaco\nHistórico de internações anteriores: 2022, 2023",
                'medicamentos_cronicos' => "Olanzapina 10mg – 1 comprimido à noite\nLamotrigina 50mg – 1 comprimido 12/12h",
                'laudos'              => "RNM de crânio 15/05/2025: Sem lesões estruturais. Parênquima encefálico preservado.\nEEG 15/05/2025: Sem atividade epileptiforme.",
            ],
            [
                'diagnosticos'        => "F10.2 – Síndrome de dependência do álcool\nF32.1 – Episódio depressivo moderado (comórbido)",
                'medicamentos_cronicos' => "Naltrexona 50mg – 1 comprimido pela manhã\nTiamina 300mg – 1 comprimido/dia (reposição vitamínica B1)",
                'laudos'              => "Enzimas hepáticas 03/07/2025: TGO 48 U/L, TGP 52 U/L (discretamente elevadas).\nGamaTGT 03/07/2025: 89 U/L (elevada, monitorar).\nEscore AUDIT-C: 10 pontos.",
            ],
            [
                'diagnosticos'        => "F20.5 – Esquizofrenia residual\nEstado atual: remissão parcial, sintomas negativos predominantes",
                'medicamentos_cronicos' => "Haloperidol decanoato 100mg/mL – 1 ampola IM a cada 28 dias\nBiperiden 2mg – 1 comprimido 12/12h",
                'laudos'              => "Escala PANSS aplicada em 22/06/2025: Subescala positiva 12, negativa 28, geral 38.\nSem intercorrências clínicas agudas registradas.",
            ],
            [
                'diagnosticos'        => "F41.0 – Transtorno de pânico (episódico paroxístico)\nF33.0 – Transtorno depressivo recorrente, episódio atual leve",
                'medicamentos_cronicos' => "Paroxetina 20mg – 1 comprimido pela manhã\nAlprazolam 0,25mg – SOS (máx 1 comprimido/dia)",
                'laudos'              => "Holter 24h 30/06/2025: Sem arritmias. Ritmo sinusal estável.\nTSH: 1,8 µUI/mL. Vitamina D: 22 ng/mL (insuficiente, iniciar reposição).",
            ],
            [
                'diagnosticos'        => "F29 – Psicose não especificada, primeiro episódio\nInvestigação etiológica em curso",
                'medicamentos_cronicos' => "Aripiprazol 10mg – 1 comprimido pela manhã\nLorazepam 1mg – SOS para agitação",
                'laudos'              => "TC de crânio 10/07/2025: Sem lesões expansivas ou hemorragia.\nSorologia HIV, VDRL e toxicológico: negativos.\nAutoantígenos anti-NMDA: aguardando resultado.",
            ],
            [
                'diagnosticos'        => "F43.1 – Transtorno de estresse pós-traumático (TEPT)\nF32.2 – Episódio depressivo grave com sintomas somáticos",
                'medicamentos_cronicos' => "Venlafaxina 75mg – 1 cápsula pela manhã\nMirtazapina 15mg – 1 comprimido à noite (melhora sono)",
                'laudos'              => "Escala PCL-5 aplicada em 01/07/2025: Escore 52 (TEPT grave).\nPressão arterial: 118/76 mmHg. FC: 82 bpm. Sem alterações cardiovasculares.",
            ],
        ];

        $historicos = [
            "Paciente com histórico de 4 internações psiquiátricas desde 2019. Primeiro episódio com alucinações auditivas e ideação persecutória aos 23 anos. Resposta parcial ao haloperidol; migrada para risperidona com melhor tolerabilidade. Histórico familiar positivo para esquizofrenia (irmão). Funcionamento pré-mórbido comprometido. Em acompanhamento no CAPS desde 2020, com irregularidade de aderência. Última internação: setembro/2024, duração de 21 dias.",
            "Primeiro episódio maníaco em 2018, com hospitalização de urgência por comportamento desinibido, gastos excessivos e insônia de 4 dias. Diagnóstico de TAB I confirmado após segundo episódio em 2020. Boa resposta ao Lítio com nível sérico mantido em 0,7–0,9 mEq/L. Tentativa de suspensão medicamentosa em 2022 resultou em recaída. Paciente atualmente cooperativa e com insight preservado.",
            "Paciente com depressão recorrente desde os 19 anos. Três episódios graves documentados (2018, 2021, 2024). Uma tentativa de suicídio em 2021 (ingestão medicamentosa), tratada em UTI com evolução favorável. Histórico de abuso sexual na infância; em psicoterapia desde 2022. Aderência medicamentosa irregular por efeitos adversos (náusea com ISRS). Suporte familiar limitado.",
            "Internação atual é a segunda no serviço. Histórico de automutilação desde os 17 anos (cortes superficiais em membros). Múltiplos atendimentos em pronto-socorro por crises dissociativas. Abandono de tratamentos anteriores por conflitos com profissionais. Vínculos relacionais instáveis. Desempregada há 8 meses. Sem uso de substâncias atualmente. Família parcialmente engajada no tratamento.",
            "Histórico de dois surtos psicóticos prévios (2020 e 2022), ambos com hospitalização. No episódio de 2022, apresentou sintomas maníacos mistos com risco de auto e heteroagressividade, necessitando contenção química. Diagnóstico de transtorno esquizoafetivo estabelecido após avaliação longitudinal. Funcionamento global comprometido; mora com a mãe. Em benefício previdenciário.",
            "Dependência de álcool com padrão pesado (>10 doses/dia). Dois episódios de síndrome de abstinência com delirium tremens em 2022 e 2023, com internação em clínica geral. Múltiplas tentativas de desintoxicação ambulatorial sem sucesso. Histórico de hepatopatia alcoólica leve. Sem suporte familiar; mora em albergue. Engajamento terapêutico frágil porém presente nesta internação.",
            "Diagnóstico de esquizofrenia estabelecido em 2015. Evolução para forma residual com predomínio de sintomas negativos (abulia, alogia, embotamento afetivo). Quatro hospitalizações entre 2015 e 2020; desde então estabilizada com antipsicótico depot. Mora com familiar cuidador. Frequenta oficina terapêutica duas vezes por semana. Sem intercorrências clínicas agudas nos últimos 3 anos.",
            "Transtorno do pânico diagnosticado aos 28 anos após investigação cardiológica negativa. Múltiplas visitas a pronto-socorro por sintomas físicos intensos. Esquiva agorafóbica progressiva que culminou em isolamento social. Tentativa de tratamento com TCC interrompida. Episódio depressivo atual é o segundo, precipitado por término de relacionamento. Sem comorbidades clínicas relevantes.",
            "Internada em caráter de urgência após crise psicótica aguda com agressividade e heteroagressão a familiar. Sem histórico psiquiátrico documentado anterior. Nega uso de drogas mas toxicológico coletado. Exames laboratoriais e de imagem em andamento para exclusão de causa orgânica. Família muito presente e colaborativa. Prognóstico a definir após resultado das investigações.",
            "Trauma de natureza física (acidente de trânsito com múltiplas fraturas) em 2022, seguido de desenvolvimento de TEPT grave. Pesadelos recorrentes, flashbacks e evitação de deslocamentos desde então. Episódio depressivo grave instalado em 2024 com anedonia, hipersonia e ideação passiva de morte. Histórico de tratamento anterior com ISRS com resposta parcial. Cirurgia ortopédica pendente para resolução de sequela.",
        ];

        $notasDiarias = [
            [
                "Paciente acordou agitada, com relato de alucinações auditivas na madrugada. Recusou café da manhã. Medicação administrada conforme prescrição. Orientada em tempo e espaço parcialmente. Sem intercorrências físicas.",
                "Melhora parcial da agitação. Participou de atividade de terapia ocupacional por 20 minutos antes de pedir para sair. Aceitou alimentação adequada. Sono referido como fragmentado. Sinais vitais estáveis.",
                "Paciente mais tranquila. Alucinações auditivas persistem mas com menor intensidade relatada. Recusou visita de familiar. Medicação aceita sem resistência. Higiênica. Sem queixas somáticas.",
                "Quadro estável. Participação em grupo terapêutico por tempo integral. Verbalizou querer melhorar. Sono de 6 horas referido. PA 120/80 mmHg, FC 76 bpm, T 36,5°C.",
                "Paciente colaborativa. Alucinações em remissão parcial. Iniciou contato com familiar por telefone. Sem necessidade de contenção. Apetite normalizado. Equipe planeja avaliação para liberação gradual.",
            ],
            [
                "Paciente em estado eufórico ao acordar, discurso acelerado e grandioso. Dormiu apenas 2 horas. Recusa medicação alegando que está curada. Necessária abordagem motivacional pela equipe.",
                "Mantida euforia com irritabilidade ao ser contrariada. Consumiu alimentação em excesso. Tentou acessar celular de outros pacientes. Equipe reforçou limites terapêuticos. Medicação administrada após negociação.",
                "Leve redução da pressão de discurso. Dormiu 4 horas. Participou de atividade artística com entusiasmo excessivo. Sem ideação de fuga ou riscos. Colabora com cuidados básicos.",
                "Melhora progressiva do quadro maníaco. Discurso mais organizado. Aceitou refletir sobre comportamentos durante o episódio. Nível de lítio coletado. Sono de 5 horas.",
                "Paciente em remissão do estado maníaco. Humor eutímico. Participou de psicoeducação sobre TAB. Sinaliza desejo de retomar atividades domésticas. Equipe avalia alta para a próxima semana.",
            ],
            [
                "Paciente com choro fácil ao acordar. Refere pesadelos e sono não reparador. Sem apetite. Permaneceu em leito a maior parte do dia. Minimamente comunicativa.",
                "Mantida hiporexia. Aceitou apenas líquidos no almoço. Equipe de enfermagem incentivou pequenas refeições. Sem ideação suicida ativa. Refere tristeza intensa e sensação de vazio.",
                "Leve melhora do humor após conversa com psicóloga. Aceitou alimentação completa no jantar. Saiu do quarto por curto período. Sono referido como melhor (5 horas).",
                "Participou de grupo de apoio emocional. Verbalizou sentimentos de culpa relacionados ao passado. Choro presente mas mais contido. Medicação aceita sem resistência.",
                "Melhora clínica consistente. Humor menos deprimido. Iniciou leitura de livro. Alimentação regular. Sem queixas somáticas. PA e demais sinais vitais dentro do esperado.",
            ],
            [
                "Paciente com automarcas recentes (arranhões superficiais em antebraço esquerdo). Objeto cortante não localizado. Equipe comunicada. Paciente verbalizou angústia intensa. Acompanhamento estreito mantido.",
                "Sem novos episódios de automutilação. Paciente irritada com a equipe por conta da supervisão aumentada. Recusou grupo terapêutico. Alimentação parcial. Sono irregular.",
                "Aceitou atendimento individual com psicóloga. Expressou sentimentos de vazio e medo de abandono. Sem ideação suicida estruturada. Colaborou com medicação.",
                "Melhora do vínculo terapêutico. Participou brevemente do grupo. Humor instável mas sem crises dissociativas. Referiu sonhos perturbadores. Higiene pessoal adequada.",
                "Quadro mais estável. Sem automutilação nos últimos 3 dias. Paciente demonstrou interesse em aprender técnicas de regulação emocional da DBT. Equipe satisfeita com a evolução.",
            ],
            [
                "Paciente com fala desorganizada e ambivalência ao acordar. Recusou medicação oralmente; administrado por IM conforme prescrição de emergência. Agitação leve contida com abordagem verbal.",
                "Efeito da medicação IM evidenciado: paciente mais calma. Discurso menos fragmentado. Aceitou refeição. Sem alucinações auditivas evidentes neste turno. Sinais vitais normais.",
                "Paciente colaborativa. Participa de forma passiva das atividades. Humor levemente elevado. Sem sintomas maníacos floridos. Aceitou medicação oral. Sono de 6 horas.",
                "Equipe médica revisou prescrição. Mantida olanzapina com lamotrigina. Paciente demonstrou melhora significativa no pensamento formal. Comunicação mais coerente.",
                "Quadro estabilizado. Paciente orientada em todas as esferas. Participação ativa em grupo. Família visitou; interação positiva observada. Planejamento de alta em discussão.",
            ],
            [
                "Paciente admitida ontem em síndrome de abstinência alcoólica leve. Tremores finos em mãos. Diaforética. Ansiosa. Thiamina e hidratação EV em curso. Monitorização CIWA-Ar a cada 4h: 12 pontos.",
                "CIWA-Ar: 8 pontos. Melhora dos tremores. Aceitou dieta leve. Mantém ansiedade moderada. Solicitou cigarros repetidamente. Orientada sobre o ambiente de internação.",
                "CIWA-Ar: 5 pontos. Sem sintomas de abstinência grave. Iniciou alimentação regular. Mais comunicativa. Participou de conversa informal com outro paciente. Sono de 4 horas.",
                "CIWA-Ar: 3 pontos. Abstinência alcoólica resolvida. Paciente engajada em conversa sobre motivação para abstinência. Recebeu material de psicoeducação sobre dependência.",
                "Paciente estável clinicamente. Participou de grupo de dependência química. Verbalizou reconhecer os danos do álcool. Solicita contato com familiar. Sono regular.",
            ],
            [
                "Paciente quieta, com embotamento afetivo marcado. Responde perguntas com monossílabos. Cuida da higiene após incentivo. Sem sintomas produtivos. Medicação aceita.",
                "Sem alterações em relação ao dia anterior. Abulia presente. Participou de terapia ocupacional com estímulo intenso; produziu pequeno trabalho manual. Alimentação completa.",
                "Mantida estabilidade. Paciente assistiu televisão por 2 horas. Sem queixas. Sono referido como adequado. Sinais vitais dentro da normalidade. Aplicação depot no prazo.",
                "Pequena melhora da expressividade facial. Fez uma pergunta espontânea durante o grupo. Equipe registra como evolução positiva. Familiar responsável atualizado.",
                "Quadro crônico estabilizado. Sem sintomas agudos. Preparo para retorno ao convívio familiar. Contato com CAPS de referência para garantir continuidade do cuidado.",
            ],
            [
                "Paciente ansiosa desde a admissão. Relata palpitações e sensação de morte iminente. Exame físico sem alterações cardiovasculares. Orientação e apoio psicológico prestados. Medicação SOS utilizada.",
                "Melhora da ansiedade aguda. Paciente mais calma após técnica de respiração ensinada pela equipe. Aceitou caminhada no corredor. Sem crises de pânico registradas neste turno.",
                "Paciente relata ter dormido 5 horas, melhor do que em casa. Humor levemente deprimido. Sem crises. Participou de grupo psicoeducativo sobre ansiedade.",
                "Dia sem intercorrências. Aceitou todas as refeições. Leu por 1 hora. Verbalizou sentir-se mais segura dentro do hospital. Equipe avalia evolução positiva.",
                "Melhora clínica consistente. Paciente demonstra estratégias de coping adquiridas. Familia informada sobre plano de alta. Encaminhamento para psicoterapia ambulatorial providenciado.",
            ],
            [
                "Paciente em primeiro dia pós-agitação. Sedada com lorazepam ontem. Acordou calma, um pouco sonolenta. Colabora com a equipe. Aguardam resultados de exames. Família presente.",
                "Resultados laboratoriais sem alterações significativas. TC crânio: normal. Paciente mais alerta. Consegue manter diálogo coerente por curtos períodos. Sem alucinações observadas.",
                "Toxicológico negativo. VDRL não reagente. Paciente orientada em tempo e espaço. Aceita medicação. Equipe revisa hipóteses diagnósticas. Familiar relata mudança de comportamento iniciada há 3 semanas.",
                "Aguardando resultado de anticorpos anti-NMDA. Paciente estável, sem surtos comportamentais. Participou de atividade supervisionada. Neurologia consultada: acompanha o caso.",
                "Quadro clínico estabilizado com medicação antipsicótica. Investigação etiológica em curso. Paciente e família receberam orientações sobre o processo diagnóstico.",
            ],
            [
                "Paciente com queixas de pesadelos e dificuldade de se orientar ao acordar. Agitação psicomotora leve no início da manhã. Medicação de rotina administrada. Apoio emocional prestado.",
                "Melhora parcial após fala com psicóloga. Paciente relatou fragmento do trauma espontaneamente. Escutada com atenção. Sem sintomas dissociativos neste turno. Alimentação regular.",
                "Paciente com humor levemente melhorado. Participou de atividade de arteterapia. Criou trabalho expressivo relacionado ao acidente. Equipe considerou positivo.",
                "Sem pesadelos relatados na noite anterior. Sono de 6 horas. Mais comunicativa com equipe. Aceita todas as refeições. Refere levemente diminuída a sensação de entorpecimento.",
                "Evolução favorável. Paciente engajada no projeto terapêutico. Avaliação ortopédica agendada. Familiar presente e atualizado. Previsão de manutenção da internação por mais 5 dias.",
            ],
        ];

        $pacientes->each(function (Paciente $paciente, int $idx) use ($fichas, $historicos, $notasDiarias, $medico, $enfermeiro) {
            $fichaData = $fichas[$idx % count($fichas)];

            FichaMedica::create([
                'paciente_id'          => $paciente->id,
                'diagnosticos'         => $fichaData['diagnosticos'],
                'medicamentos_cronicos' => $fichaData['medicamentos_cronicos'],
                'laudos'               => $fichaData['laudos'],
                'atualizado_por'       => $medico,
            ]);

            ProntuarioHistorico::create([
                'paciente_id'    => $paciente->id,
                'conteudo'       => $historicos[$idx % count($historicos)],
                'atualizado_por' => $medico,
            ]);

            $notas = $notasDiarias[$idx % count($notasDiarias)];
            foreach ($notas as $daysAgo => $conteudo) {
                $data = Carbon::today()->subDays(count($notas) - 1 - $daysAgo)->toDateString();
                ProntuarioDiario::create([
                    'paciente_id'    => $paciente->id,
                    'data'           => $data,
                    'conteudo'       => $conteudo,
                    'atualizado_por' => $enfermeiro,
                ]);
            }
        });

        $this->command->info('Fichas médicas, prontuários diários e históricos criados com sucesso.');
    }
}
