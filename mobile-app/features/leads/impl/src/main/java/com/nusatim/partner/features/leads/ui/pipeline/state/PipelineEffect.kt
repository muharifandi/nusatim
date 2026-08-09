package com.nusatim.partner.features.leads.ui.pipeline.state

import com.nusatim.partner.core.architecture.mvi.UiEffect

sealed interface PipelineEffect : UiEffect {
    data class ShowError(val message: String) : PipelineEffect
}
