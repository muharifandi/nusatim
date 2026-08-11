package com.nusatim.partner.features.leads.ui.pipeline

import androidx.lifecycle.viewModelScope
import com.nusatim.partner.core.architecture.mvi.BaseViewModel
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.features.leads.domain.usecase.GetPipelineUseCase
import com.nusatim.partner.features.leads.domain.usecase.UpdateLeadStatusUseCase
import com.nusatim.partner.features.leads.ui.pipeline.state.PipelineEffect
import com.nusatim.partner.features.leads.ui.pipeline.state.PipelineIntent
import com.nusatim.partner.features.leads.ui.pipeline.state.PipelineState
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.collectLatest
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class PipelineViewModel @Inject constructor(
    private val getPipelineUseCase: GetPipelineUseCase,
    private val updateLeadStatusUseCase: UpdateLeadStatusUseCase
) : BaseViewModel<PipelineState, PipelineIntent, PipelineEffect>(PipelineState()) {

    init {
        processIntent(PipelineIntent.LoadPipeline)
    }

    override fun processIntent(intent: PipelineIntent) {
        when (intent) {
            is PipelineIntent.LoadPipeline -> loadPipeline()
            is PipelineIntent.FilterPipeline -> {
                setState { copy(serviceId = intent.serviceId, dateFrom = intent.dateFrom, dateTo = intent.dateTo) }
                loadPipeline()
            }
            is PipelineIntent.UpdateStatus -> updateStatus(intent.id, intent.status)
        }
    }

    private fun loadPipeline() {
        viewModelScope.launch {
            val currentState = state.value
            getPipelineUseCase(
                serviceId = currentState.serviceId,
                dateFrom = currentState.dateFrom,
                dateTo = currentState.dateTo
            ).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> setState {
                        copy(isLoading = false, pipeline = result.data, error = null)
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false, error = result.message) }
                        sendEffect { PipelineEffect.ShowError(result.message) }
                    }
                }
            }
        }
    }

    private fun updateStatus(id: Int, status: String) {
        viewModelScope.launch {
            updateLeadStatusUseCase(id, status).collectLatest { result ->
                when (result) {
                    is ResultState.Loading -> setState { copy(isLoading = true) }
                    is ResultState.Success -> {
                        loadPipeline() // Refresh board
                    }
                    is ResultState.Error -> {
                        setState { copy(isLoading = false) }
                        sendEffect { PipelineEffect.ShowError(result.message) }
                    }
                }
            }
        }
    }
}
