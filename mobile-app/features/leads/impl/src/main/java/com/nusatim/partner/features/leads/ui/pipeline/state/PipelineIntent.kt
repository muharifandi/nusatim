package com.nusatim.partner.features.leads.ui.pipeline.state

import com.nusatim.partner.core.architecture.mvi.UiIntent

sealed interface PipelineIntent : UiIntent {
    data object LoadPipeline : PipelineIntent
    data class FilterPipeline(val serviceId: Int?, val dateFrom: String?, val dateTo: String?) : PipelineIntent
    data class UpdateStatus(val id: Int, val status: String) : PipelineIntent
}
