package com.nusatim.partner.features.leads.ui.pipeline.state

import com.nusatim.partner.core.architecture.mvi.UiState
import com.nusatim.partner.core.model.dto.PipelineResponse

data class PipelineState(
    val isLoading: Boolean = false,
    val pipeline: PipelineResponse? = null,
    val error: String? = null,
    val serviceId: Int? = null,
    val dateFrom: String? = null,
    val dateTo: String? = null
) : UiState
