package com.nusatim.partner.features.finance.ui.commissions

import androidx.lifecycle.viewModelScope
import com.nusatim.partner.core.architecture.mvi.BaseViewModel
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.features.finance.domain.usecase.GetCommissionDetailUseCase
import com.nusatim.partner.features.finance.domain.usecase.GetCommissionsUseCase
import com.nusatim.partner.features.finance.ui.commissions.state.CommissionsEffect
import com.nusatim.partner.features.finance.ui.commissions.state.CommissionsIntent
import com.nusatim.partner.features.finance.ui.commissions.state.CommissionsState
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.collectLatest
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class CommissionsViewModel @Inject constructor(
    private val getCommissionsUseCase: GetCommissionsUseCase,
    private val getCommissionDetailUseCase: GetCommissionDetailUseCase
) : BaseViewModel<CommissionsState, CommissionsIntent, CommissionsEffect>(CommissionsState()) {

    init {
        processIntent(CommissionsIntent.LoadCommissions())
    }

    override fun processIntent(intent: CommissionsIntent) {
        when (intent) {
            is CommissionsIntent.LoadCommissions -> {
                setState { copy(statusFilter = intent.status) }
                loadCommissions(intent.page)
            }
            is CommissionsIntent.LoadCommissionDetail -> loadCommissionDetail(intent.id)
        }
    }

    private fun loadCommissions(page: Int) {
        viewModelScope.launch {
            getCommissionsUseCase(
                status = state.value.statusFilter,
                page = page
            ).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> setState {
                        copy(isLoading = false, commissionsResponse = result.data, error = null)
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false, error = result.message) }
                        sendEffect { CommissionsEffect.ShowError(result.message) }
                    }
                }
            }
        }
    }

    private fun loadCommissionDetail(id: Int) {
        viewModelScope.launch {
            getCommissionDetailUseCase(id).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> setState {
                        copy(isLoading = false, selectedCommission = result.data, error = null)
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false, error = result.message) }
                        sendEffect { CommissionsEffect.ShowError(result.message) }
                    }
                }
            }
        }
    }
}
