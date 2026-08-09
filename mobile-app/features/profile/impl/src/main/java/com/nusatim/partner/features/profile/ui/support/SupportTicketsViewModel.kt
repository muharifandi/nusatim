package com.nusatim.partner.features.profile.ui.support

import androidx.lifecycle.viewModelScope
import com.nusatim.partner.core.architecture.mvi.BaseViewModel
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.features.profile.domain.usecase.CreateSupportTicketUseCase
import com.nusatim.partner.features.profile.domain.usecase.GetSupportTicketDetailUseCase
import com.nusatim.partner.features.profile.domain.usecase.GetSupportTicketsUseCase
import com.nusatim.partner.features.profile.ui.support.state.*
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.collectLatest
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class SupportTicketsViewModel @Inject constructor(
    private val getSupportTicketsUseCase: GetSupportTicketsUseCase,
    private val getSupportTicketDetailUseCase: GetSupportTicketDetailUseCase,
    private val createSupportTicketUseCase: CreateSupportTicketUseCase
) : BaseViewModel<SupportTicketsState, SupportTicketsIntent, SupportTicketsEffect>(SupportTicketsState()) {

    init {
        processIntent(SupportTicketsIntent.LoadTickets())
    }

    override fun processIntent(intent: SupportTicketsIntent) {
        when (intent) {
            is SupportTicketsIntent.LoadTickets -> loadTickets(intent.page)
            is SupportTicketsIntent.LoadDetail -> loadDetail(intent.id)
            is SupportTicketsIntent.CreateTicket -> createTicket(intent.subject, intent.description)
        }
    }

    private fun loadTickets(page: Int) {
        viewModelScope.launch {
            getSupportTicketsUseCase(page = page).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> setState { 
                        copy(isLoading = false, ticketsResponse = result.data, error = null) 
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false, error = result.message) }
                        sendEffect { SupportTicketsEffect.ShowError(result.message) }
                    }
                }
            }
        }
    }

    private fun loadDetail(id: Int) {
        viewModelScope.launch {
            getSupportTicketDetailUseCase(id).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> setState { 
                        copy(isLoading = false, selectedDetail = result.data, error = null) 
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false, error = result.message) }
                        sendEffect { SupportTicketsEffect.ShowError(result.message) }
                    }
                }
            }
        }
    }

    private fun createTicket(subject: String, description: String) {
        viewModelScope.launch {
            createSupportTicketUseCase(subject, description).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> {
                        setState { copy(isLoading = false) }
                        sendEffect { SupportTicketsEffect.SuccessCreate }
                        loadTickets(1) // Refresh
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false) }
                        sendEffect { SupportTicketsEffect.ShowError(result.message) }
                    }
                }
            }
        }
    }
}
