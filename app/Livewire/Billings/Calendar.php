<?php

namespace App\Livewire\Billings;

use App\Models\Billings\AssinaturaAtual;
use App\Models\Billings\AssinaturaHistorico;
use App\Models\Billings\AssinaturaPagamento;
use App\Models\Billings\AssinaturaLog;
use App\Models\Igreja;
use Livewire\Component;
use Carbon\Carbon;

class Calendar extends Component
{
    public $events = [];
    public $filterType = 'all'; // all, start_dates, end_dates, payments, new_contracts
    public $selectedMonth;
    public $selectedYear;

    public function mount()
    {
        $this->selectedMonth = now()->month;
        $this->selectedYear = now()->year;
        $this->loadEvents();
    }

    public function updatedFilterType()
    {
        $this->loadEvents();
        $this->dispatch('calendar-filter-changed');
    }

    public function loadEvents()
    {
        $this->events = [];

        // Carregar eventos baseado no filtro
        switch ($this->filterType) {
            case 'start_dates':
                $this->loadStartDates();
                break;
            case 'end_dates':
                $this->loadEndDates();
                break;
            case 'payments':
                $this->loadPayments();
                break;
            case 'new_contracts':
                $this->loadNewContracts();
                break;
            default:
                $this->loadAllEvents();
                break;
        }
    }

    private function loadAllEvents()
    {
        $this->loadStartDates();
        $this->loadEndDates();
        $this->loadPayments();
        $this->loadNewContracts();
    }

    private function loadStartDates()
    {
        $startDates = AssinaturaAtual::with(['igreja', 'pacote'])
            ->whereYear('data_inicio', $this->selectedYear)
            ->whereMonth('data_inicio', $this->selectedMonth)
            ->get();

        foreach ($startDates as $assinatura) {
            $this->events[] = [
                'id' => 'start_' . $assinatura->igreja_id,
                'title' => '📅 Início: ' . $assinatura->igreja->nome,
                'start' => $assinatura->data_inicio->format('Y-m-d'),
                'backgroundColor' => '#28a745',
                'borderColor' => '#28a745',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => 'start_date',
                    'igreja' => $assinatura->igreja->nome,
                    'pacote' => $assinatura->pacote->nome ?? 'N/A',
                    'valor' => $assinatura->pacote->preco ?? 0,
                    'vitalicio' => $assinatura->vitalicio
                ]
            ];
        }
    }

    private function loadEndDates()
    {
        $endDates = AssinaturaAtual::with(['igreja', 'pacote'])
            ->whereNotNull('data_fim')
            ->where('vitalicio', false)
            ->whereYear('data_fim', $this->selectedYear)
            ->whereMonth('data_fim', $this->selectedMonth)
            ->get();

        foreach ($endDates as $assinatura) {
            $backgroundColor = $assinatura->data_fim->isPast() ? '#dc3545' : '#ffc107';

            $this->events[] = [
                'id' => 'end_' . $assinatura->igreja_id,
                'title' => '⏰ Fim: ' . $assinatura->igreja->nome,
                'start' => $assinatura->data_fim->format('Y-m-d'),
                'backgroundColor' => $backgroundColor,
                'borderColor' => $backgroundColor,
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => 'end_date',
                    'igreja' => $assinatura->igreja->nome,
                    'pacote' => $assinatura->pacote->nome ?? 'N/A',
                    'status' => $assinatura->data_fim->isPast() ? 'expirado' : 'ativo',
                    'dias_restantes' => $assinatura->data_fim->isFuture() ?
                        $assinatura->data_fim->diffInDays(now()) : 0
                ]
            ];
        }
    }

    private function loadPayments()
    {
        $payments = AssinaturaPagamento::with(['igreja', 'assinatura.pacote'])
            ->where('status', 'confirmado')
            ->whereYear('data_pagamento', $this->selectedYear)
            ->whereMonth('data_pagamento', $this->selectedMonth)
            ->get();

        foreach ($payments as $pagamento) {
            $this->events[] = [
                'id' => 'payment_' . $pagamento->id,
                'title' => '💰 AOA ' . number_format($pagamento->valor, 2, ',', '.') . ' - ' . $pagamento->igreja->nome,
                'start' => $pagamento->data_pagamento->format('Y-m-d'),
                'backgroundColor' => '#17a2b8',
                'borderColor' => '#17a2b8',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => 'payment',
                    'igreja' => $pagamento->igreja->nome,
                    'valor' => $pagamento->valor,
                    'metodo' => $pagamento->metodo_pagamento,
                    'referencia' => $pagamento->referencia
                ]
            ];
        }
    }

    private function loadNewContracts()
    {
        $newContracts = AssinaturaLog::with(['igreja', 'pacote'])
            ->where('acao', 'criado')
            ->whereYear('data_acao', $this->selectedYear)
            ->whereMonth('data_acao', $this->selectedMonth)
            ->get();

        foreach ($newContracts as $log) {
            $this->events[] = [
                'id' => 'contract_' . $log->id,
                'title' => '🎉 Novo: ' . $log->igreja->nome,
                'start' => $log->data_acao->format('Y-m-d'),
                'backgroundColor' => '#6f42c1',
                'borderColor' => '#6f42c1',
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => 'new_contract',
                    'igreja' => $log->igreja->nome,
                    'pacote' => $log->pacote->nome ?? 'N/A',
                    'usuario' => $log->usuario->name ?? 'Sistema'
                ]
            ];
        }
    }

    public function changeMonth($direction)
    {
        if ($direction === 'prev') {
            $this->selectedMonth--;
            if ($this->selectedMonth < 1) {
                $this->selectedMonth = 12;
                $this->selectedYear--;
            }
        } else {
            $this->selectedMonth++;
            if ($this->selectedMonth > 12) {
                $this->selectedMonth = 1;
                $this->selectedYear++;
            }
        }
        $this->loadEvents();
        $this->dispatch('calendar-month-changed');
    }

    public function refreshCalendar()
    {
        $this->loadEvents();
        $this->dispatch('calendar-refreshed');
    }

    public function getCurrentMonthName()
    {
        return \Carbon\Carbon::create($this->selectedYear, $this->selectedMonth)->format('F Y');
    }

    public function render()
    {
        return view('billings.calendar');
    }
}
