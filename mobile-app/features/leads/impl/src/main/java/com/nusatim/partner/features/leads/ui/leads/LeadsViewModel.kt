package com.nusatim.partner.features.leads.ui.leads

import androidx.lifecycle.viewModelScope
import com.nusatim.partner.core.architecture.mvi.BaseViewModel
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.features.leads.domain.usecase.GetLeadsUseCase
import com.nusatim.partner.features.leads.ui.leads.state.*
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.collectLatest
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class LeadsViewModel @Inject constructor(
    private val getLeadsUseCase: GetLeadsUseCase
) : BaseViewModel<LeadsState, LeadsIntent, LeadsEffect>(LeadsState()) {

    init {
        processIntent(LeadsIntent.LoadLeads())
    }

    override fun processIntent(intent: LeadsIntent) {
        when (intent) {
            is LeadsIntent.LoadLeads -> loadLeads(page = intent.page)
            is LeadsIntent.SearchLeads -> {
                setState { copy(searchQuery = intent.query) }
                loadLeads()
            }
            is LeadsIntent.FilterLeads -> {
                setState { copy(statusFilter = intent.status) }
                loadLeads()
            }
        }
    }

    private fun loadLeads(page: Int = 1) {
        viewModelScope.launch {
            val currentState = state.value
            getLeadsUseCase(
                status = currentState.statusFilter,
                search = currentState.searchQuery,
                page = page
            ).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> setState { 
                        copy(isLoading = false, leadsResponse = result.data, error = null) 
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false, error = result.message) }
                        sendEffect { LeadsEffect.ShowError(result.message) }
                    }
                }
            }
        }
    }
}
