<?php

namespace Modules\Voting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\User\Models\Ranking;
use Modules\User\Models\RankingResponse;
use Modules\Subscriptions\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class VotingController extends Controller
{
    /**
     * Display the voting page
     * Busca o ranking ativo para a data atual
     */
    public function index()
    {
        $user = Auth::user();
        $hasAccess = $this->checkUserAccess($user);

        // Buscar ranking ativo (dentro das datas)
        $currentDate = now()->toDateString();
        $activeRanking = Ranking::where('start_date', '<=', $currentDate)
            ->where('end_date', '>=', $currentDate)
            ->where('status', 1)
            ->first();

        $rankingId = $activeRanking ? $activeRanking->id : null;

        return view('voting::index', compact('user', 'hasAccess', 'activeRanking', 'rankingId'));
    }

    /**
     * Check if user has access to voting feature
     */
    public function checkAccess(Request $request)
    {
        $user = Auth::user();
        $hasAccess = $this->checkUserAccess($user);

        return response()->json([
            'has_access' => $hasAccess,
            'message' => !$hasAccess ? 'Você precisa comprar o acesso a esta feature' : 'Acesso liberado'
        ]);
    }

    /**
     * Get top 3 current ranking
     * Busca as opções com mais votos do ranking ativo
     */
    public function getTop3(Request $request)
    {
        try {
            $currentDate = now()->toDateString();
            $ranking = Ranking::where('start_date', '<=', $currentDate)
                ->where('end_date', '>=', $currentDate)
                ->where('status', 1)
                ->first();

            if (!$ranking) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'Nenhum ranking ativo no momento'
                ]);
            }

            // Decodificar contents - pode vir como string do banco
            $contents = is_string($ranking->contents) 
                ? json_decode($ranking->contents, true) ?? []
                : $ranking->contents ?? [];
            
            if (empty($contents)) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'Ranking sem conteúdo'
                ]);
            }
            
            // Ordena por votos (decrescente) e pega top 3
            usort($contents, function($a, $b) {
                return ((int)($b['votes'] ?? 0)) <=> ((int)($a['votes'] ?? 0));
            });

            $top3 = array_slice($contents, 0, 3);
            
            // Calcular total de votos
            $totalVotes = 0;
            foreach ($contents as $content) {
                $totalVotes += (int)($content['votes'] ?? 0);
            }
            
            // Formata a resposta
            $formatted = array_map(function($item, $index) use ($totalVotes) {
                $itemVotes = (int)($item['votes'] ?? 0);
                $percentage = $totalVotes > 0 ? round(($itemVotes / $totalVotes) * 100, 1) : 0;
                
                return [
                    'id' => $item['slug'] ?? uniqid(),
                    'position' => $index + 1,
                    'name' => $item['name'] ?? $item['title'] ?? 'Opção sem nome',
                    'image' => $item['image_url'] ?? null,
                    'total_votes' => $itemVotes,
                    'percentage' => $percentage,
                ];
            }, $top3, array_keys($top3));
            
            // Converter para array numérico
            $formatted = array_values($formatted);

            return response()->json([
                'success' => true,
                'data' => $formatted,
                'ranking_id' => $ranking->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all candidates from active ranking
     */
    public function getAllCandidates(Request $request)
    {
        try {
            $currentDate = now()->toDateString();
            $ranking = Ranking::where('start_date', '<=', $currentDate)
                ->where('end_date', '>=', $currentDate)
                ->where('status', 1)
                ->first();

            if (!$ranking) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'Nenhum ranking ativo no momento'
                ]);
            }

            // Decodificar contents - pode vir como string do banco
            $contents = is_string($ranking->contents) 
                ? json_decode($ranking->contents, true) ?? []
                : $ranking->contents ?? [];
            
            if (empty($contents)) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'Ranking sem conteúdo'
                ]);
            }
            
            // Formata as opções para exibição
            $candidates = array_map(function($item) use ($contents) {
                // Calcular total de votos
                $totalVotes = 0;
                foreach ($contents as $content) {
                    $totalVotes += (int)($content['votes'] ?? 0);
                }
                
                $itemVotes = (int)($item['votes'] ?? 0);
                $percentage = $totalVotes > 0 ? round(($itemVotes / $totalVotes) * 100, 1) : 0;
                
                return [
                    'id' => $item['slug'] ?? uniqid(),
                    'name' => $item['name'] ?? $item['title'] ?? 'Opção sem nome',
                    'image' => $item['image_url'] ?? null,
                    'votes' => $itemVotes,
                    'percentage' => $percentage,
                ];
            }, $contents);
            
            // Converter para array numérico (não objeto)
            $candidates = array_values($candidates);

            return response()->json([
                'success' => true,
                'data' => $candidates,
                'ranking_id' => $ranking->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a vote for a ranking option
     */
    public function storeVote(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$this->checkUserAccess($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sem acesso a esta feature'
                ], 403);
            }

            $validated = $request->validate([
                'content_slug' => 'required|string',
            ]);

            $currentDate = now()->toDateString();
            $ranking = Ranking::where('start_date', '<=', $currentDate)
                ->where('end_date', '>=', $currentDate)
                ->where('status', 1)
                ->first();

            if (!$ranking) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhum ranking ativo no momento'
                ], 404);
            }

            // Check if user has already voted 3 times in this ranking period
            $voteCount = RankingResponse::where('user_id', $user->id)
                ->where('ranking_id', $ranking->id)
                ->whereNotNull('content_slug')
                ->where('content_slug', '!=', '')
                ->count();

            if ($voteCount >= 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você atingiu o limite de 3 votos para este período de votação'
                ], 429);
            }

            // Verifica se usuário já votou HOJE neste ranking
            $alreadyVoted = RankingResponse::where('user_id', $user->id)
                ->where('ranking_id', $ranking->id)
                ->where('response_date', $currentDate)
                ->first();

            // Se já votou hoje, remove voto anterior antes de contar novo voto
            if ($alreadyVoted) {
                // Remove voto anterior
                $contents = json_decode($ranking->contents, true) ?? [];
                foreach ($contents as &$item) {
                    if (($item['slug'] ?? null) === $alreadyVoted->content_slug) {
                        $item['votes'] = max(0, ($item['votes'] ?? 0) - 1);
                    }
                }
                $ranking->contents = json_encode($contents);
            }

            // Incrementar voto do candidato
            $contents = json_decode($ranking->contents, true) ?? [];
            $found = false;
            
            foreach ($contents as &$item) {
                if (($item['slug'] ?? null) === $validated['content_slug']) {
                    $item['votes'] = ($item['votes'] ?? 0) + 1;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                return response()->json([
                    'success' => false,
                    'message' => 'Opção não encontrada'
                ], 404);
            }

            $ranking->contents = json_encode($contents);
            $ranking->save();

            // Registrar resposta
            if ($alreadyVoted) {
                $alreadyVoted->update([
                    'content_slug' => $validated['content_slug'],
                    'response_date' => $currentDate,
                ]);
            } else {
                RankingResponse::create([
                    'user_id' => $user->id,
                    'ranking_id' => $ranking->id,
                    'content_slug' => $validated['content_slug'],
                    'response_date' => $currentDate,
                ]);
            }

            // Retornar top 3 atualizado
            return $this->getTop3($request);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's current vote
     */
    public function getUserVote(Request $request)
    {
        try {
            $user = Auth::user();
            $currentDate = now()->toDateString();
            $ranking = Ranking::where('start_date', '<=', $currentDate)
                ->where('end_date', '>=', $currentDate)
                ->where('status', 1)
                ->first();

            if (!$ranking) {
                return response()->json([
                    'success' => true,
                    'voted' => false,
                    'vote' => null
                ]);
            }

            $vote = RankingResponse::where('user_id', $user->id)
                ->where('ranking_id', $ranking->id)
                ->where('response_date', $currentDate)
                ->first();

            return response()->json([
                'success' => true,
                'voted' => !!$vote,
                'vote' => $vote ? $vote->content_slug : null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store user suggestion for a creator
     */
    public function storeSuggestion(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$this->checkUserAccess($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sem acesso a esta feature'
                ], 403);
            }

            $validated = $request->validate([
                'ranking_id' => 'required|exists:rankings,id',
                'sugestion_name' => 'required|string|max:255',
                'sugestion_link' => 'required|url|max:500',
            ]);

            $currentDate = now()->toDateString();
            
            // Find the ranking
            $ranking = Ranking::findOrFail($validated['ranking_id']);

            // Check if ranking is active
            if (!($ranking->start_date <= $currentDate && $ranking->end_date >= $currentDate)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este período de votação não está mais ativo'
                ], 404);
            }

            // Create suggestion record
            RankingResponse::create([
                'user_id' => $user->id,
                'ranking_id' => $ranking->id,
                'response_date' => $currentDate,
                'sugestion_name' => $validated['sugestion_name'],
                'sugestion_link' => $validated['sugestion_link'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Sugestão registrada com sucesso! Obrigado por contribuir com a comunidade.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar sugestão: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if user has voting access
     */
    private function checkUserAccess($user)
    {
        if (!$user) {
            return false;
        }

        try {
            return Subscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->whereHas('plan', function ($query) {
                    $query->where('identifier', 'community-voting')
                        ->orWhere('identifier', 'voting')
                        ->orWhere('identifier', 'voting-community')
                        ->orWhere('name', 'like', '%Voting%')
                        ->orWhere('name', 'like', '%Community%');
                })
                ->exists();
        } catch (\Exception $e) {
            return false;
        }
    }
}
