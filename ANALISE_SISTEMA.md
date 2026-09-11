# Análise do Sistema InamexControl

## Visão Geral

Sistema de controle de medicação hospitalar para a INAMEX, construído em Laravel 12 + Livewire com banco de dados PostgreSQL (Supabase). Acesso fechado: sem área pública, todo usuário precisa de aprovação do admin.

---

## Funcionalidades Disponíveis

### Dashboard
- Exibe data e saudação com o nome do usuário logado
- Status do diário do dia (aberto / encerrado)
- Cards com: total de internas ativas, doses do dia (manhã e tarde), percentual administrado por turno
- Lista de internas com doses pendentes no dia
- Alertas de internas sem prontuário diário preenchido
- Alertas de internas sem prescrição ativa
- Feed das últimas 10 doses administradas no dia (com medicamento, paciente e quem administrou)

---

### Internas (Pacientes)
**Lista (`/pacientes`)**
- Busca por nome ou número de prontuário
- Filtro por status: ativas / inativas / todas
- Paginação (15 por página)
- Ações: visualizar, editar, excluir (admin/diretor)

**Cadastro/Edição (`/pacientes/create`, `/pacientes/{id}/edit`)**
- Nome, número de prontuário (único), quarto, status ativo/inativo
- Upload de foto do paciente (imagem, max 4 MB)

**Página da Interna (`/pacientes/{id}`)**
Quatro abas integradas na mesma tela:
1. **Ficha Médica** — diagnósticos, medicamentos crônicos, laudos (edição in-place)
2. **Prontuário Diário** — notas diárias dos últimos 14 dias com navegação por data, indicação visual de dias com conteúdo, exibe quem editou por último
3. **Prontuário Histórico** — texto livre com histórico clínico completo da interna
4. **Registros de Medicação** — painel com os registros de medicação do dia, navegável por data, marcação de administrado/não administrado e observações

---

### Medicamentos
- Lista com busca por nome, paginação
- Cadastro/edição: nome, concentração, via de administração, ativo/inativo
- Exclusão protegida: se o medicamento estiver em uso em prescrições, exibe mensagem de erro em vez de excluir

---

### Prescrições
- Lista com busca por nome da interna, filtro ativas/inativas
- Cadastro: seleciona interna, medicamento, dose, horário (manhã ou tarde)
- A prescrição é inativada (nunca excluída) para preservar o histórico de registros de medicação
- Pré-seleção da interna quando chamado a partir da página da interna

---

### Registros de Medicação (`/registros`)
- Tela operacional diária para a equipe de enfermagem
- Navegar entre datas passadas (read-only se o dia estiver encerrado)
- Marcar administrado / não administrado por registro
- Adicionar observação em cada registro
- Geração automática de registros pendentes ao acessar a tela do dia atual
- Bloqueio de edição quando o dia está encerrado

---

### Exportação / Relatórios (`/relatorios`)
- Selecionar interna ativa e período de datas
- **Ficha Médica PDF** — diagnósticos, medicamentos crônicos e laudos da interna
- **Prontuário Diário PDF** — todas as notas diárias do período selecionado
- **Relatório Completo PDF** — ficha médica + histórico + todos os prontuários diários

---

### Usuários (`/usuarios`)
- Lista com busca por nome ou e-mail, paginação
- Cargos disponíveis: admin, diretor, médico, enfermeiro, chefe de enfermagem, técnico, pendente
- Criação de conta pelo admin ou diretor (senha obrigatória na criação, opcional na edição)
- Diretor não pode editar nem desativar contas de admin
- Desativar acesso (registro preservado para auditoria)
- Reativar acesso

---

### Backup (`/backup`)
- Geração de dump completo do banco via `pg_dump`
- Download do arquivo `.dump` com data/hora no nome
- Ação registrada na trilha de auditoria
- Acesso restrito a admin

---

### Perfil (`/profile`)
- Editar nome e e-mail
- Alterar senha
- Excluir conta

---

### Controle de Acesso por Cargo

> **Legenda:** ✅ permitido · 👁️ somente leitura · ❌ sem acesso

#### Internas e Medicamentos

| Ação | admin | diretor | médico | chefe enf. | enfermeiro | técnico |
|---|:---:|:---:|:---:|:---:|:---:|:---:|
| Ver internas (lista e detalhe) | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Criar / editar / excluir interna | ✅ | ❌ | ❌ | ✅ | ❌ | ❌ |
| Ver medicamentos | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Criar / editar / excluir medicamento | ✅ | ❌ | ✅ | ✅ | ❌ | ❌ |
| Ver prescrições | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Criar / editar / inativar prescrição | ✅ | ❌ | ✅ | ❌ | ❌ | ❌ |

#### Prontuários e Fichas (dentro da página da interna)

| Ação | admin | diretor | médico | chefe enf. | enfermeiro | técnico |
|---|:---:|:---:|:---:|:---:|:---:|:---:|
| Ver ficha médica | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Criar / editar ficha médica | ✅ | ❌ | ✅ | ✅ | ❌ | ❌ |
| Excluir ficha médica | ✅ | ❌ | ❌ | ✅ | ❌ | ❌ |
| Ver prontuário diário | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Criar / editar prontuário diário | ✅ | ❌ | ❌ | ✅ | ✅ | ❌ |
| Excluir prontuário diário | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Ver prontuário histórico | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Criar / editar prontuário histórico | ✅ | ❌ | ❌ | ✅ | ❌ | ❌ |
| Excluir prontuário histórico | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |

#### Registros de Medicação

| Ação | admin | diretor | médico | chefe enf. | enfermeiro | técnico |
|---|:---:|:---:|:---:|:---:|:---:|:---:|
| Ver registros | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Marcar administrado / observação | ✅ | ❌ | ✅ | ✅ | ✅ | ✅ |
| Excluir registro (dia aberto) | ✅ | ❌ | ❌ | ✅ | ❌ | ❌ |

#### Gestão do Sistema

| Ação | admin | diretor | médico | chefe enf. | enfermeiro | técnico |
|---|:---:|:---:|:---:|:---:|:---:|:---:|
| Exportar relatórios PDF | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Ver e gerenciar usuários | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| Criar usuário | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| Editar / desativar usuário | ✅ | ✅ ¹ | ❌ | ❌ | ❌ | ❌ |
| Ver auditoria (quem fez o quê) | ✅ | ✅ | ❌ | ✅ | ❌ | ❌ |
| Encerrar diário manualmente | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Backup do banco | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ |

> ¹ Diretor não pode editar nem desativar contas de admin, e não pode desativar a própria conta.

---

### Automações
- **Encerramento automático do diário** (`diario:encerrar`): roda todo dia à meia-noite via agendador do Laravel. Encerra o dia anterior, abre o novo dia e gera os registros de medicação pendentes para todas as prescrições ativas.
- **Trilha de auditoria**: toda ação sensível (marcar dose, encerrar diário, gerar backup) é registrada na tabela `logs_auditoria` com usuário, IP, user-agent e dados antes/depois.

---

## Problemas e Sugestões de Melhoria

### Bugs

**1. Encerramento manual do diário sem rota acessível**
O componente `Diario\Show` (com botão de encerramento manual) existe no código mas não há rota registrada para ele em `routes/web.php`. Admin e diretor não conseguem encerrar o dia manualmente pela interface — só pelo agendador automático.

*Correção:* adicionar em `routes/web.php`:
```php
Route::get('/diario', App\Livewire\Diario\Show::class)->name('diario.show');
```
E um link no menu ou no dashboard para `/diario`.

---

**2. Exportação Excel existe no código mas não está disponível na UI**
As classes `RegistrosMedicacaoExport` e `PacientesExport` estão implementadas mas não estão conectadas a nenhuma rota ou botão na tela de Exportação — só os PDFs são acessíveis.

*Correção:* adicionar botão "Exportar Excel" na tela `/relatorios` chamando o export via Livewire.

---

### Melhorias Pequenas

**3. "Profile" e "Log Out" em inglês no menu**
O menu exibe `Profile` e `Log Out` enquanto o resto do sistema está em português.

*Correção:* trocar para `Perfil` e `Sair` no `navigation.blade.php`.

---

**4. Link "Criar conta" na tela de login pode confundir**
O botão "Criar conta" está visível na tela de login, mas contas criadas por auto-registro nascem com cargo `pendente` e não conseguem acessar nada. Usuários podem se registrar achando que terão acesso imediato.

*Sugestão:* substituir o link por um texto explicativo como *"Solicite acesso ao administrador do sistema"*, ou ocultar o link completamente (já que o fluxo correto é o admin criar contas pelo painel de Usuários).

---

**5. Sem notificação de usuários pendentes**
Quando alguém se registra com cargo `pendente`, o admin não recebe nenhuma indicação — nem no dashboard nem no menu de Usuários.

*Sugestão:* adicionar um badge numérico no menu "Usuários" mostrando a contagem de usuários com cargo `pendente`.

---

**6. Relatórios não permitem selecionar internas inativas**
Na tela de Exportação, o dropdown só exibe internas com `ativa = true`. Se uma interna foi alta e inativada, não é possível exportar o relatório dela.

*Sugestão:* adicionar um toggle "incluir inativas" no filtro da tela de Exportação.

---

**7. Backup depende de `pg_dump` instalado no servidor**
A função de backup usa `pg_dump` via chamada de processo. Em hospedagem compartilhada ou containers sem o cliente PostgreSQL instalado, o backup falhará silenciosamente (só registrará erro no log).

*Para o deploy no Railway:* garantir que o Dockerfile ou buildpack instale `postgresql-client`.

---

**8. Agendador requer configuração no servidor**
O `diario:encerrar` só roda se houver um cron job executando `php artisan schedule:run` a cada minuto no servidor. Sem isso, o dia nunca encerra automaticamente.

*Para o Railway:* configurar um serviço de cron ou worker separado com o comando `php artisan schedule:work`.
