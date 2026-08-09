package com.nusatim.partner.features.projects.ui.board.state

import com.nusatim.partner.core.architecture.mvi.UiIntent

sealed interface ProjectsIntent : UiIntent {
    data class LoadProjects(val page: Int = 1) : ProjectsIntent
    data class LoadProjectDetail(val id: Int) : ProjectsIntent
    data class ClaimProject(val id: Int) : ProjectsIntent
    data class CancelClaimProject(val id: Int) : ProjectsIntent
}
