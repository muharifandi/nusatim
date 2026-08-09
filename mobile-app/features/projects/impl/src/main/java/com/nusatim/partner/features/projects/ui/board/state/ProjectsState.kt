package com.nusatim.partner.features.projects.ui.board.state

import com.nusatim.partner.core.architecture.mvi.UiState
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import com.nusatim.partner.core.model.dto.ProjectResponse

data class ProjectsState(
    val isLoading: Boolean = false,
    val projectsResponse: PagedBaseResponse<ProjectResponse>? = null,
    val selectedProject: ProjectResponse? = null,
    val error: String? = null
) : UiState
