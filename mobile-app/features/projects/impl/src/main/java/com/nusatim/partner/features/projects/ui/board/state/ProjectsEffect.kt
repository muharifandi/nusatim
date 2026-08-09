package com.nusatim.partner.features.projects.ui.board.state

import com.nusatim.partner.core.architecture.mvi.UiEffect

sealed interface ProjectsEffect : UiEffect {
    data class ShowError(val message: String) : ProjectsEffect
    data class ShowSuccess(val message: String) : ProjectsEffect
}
