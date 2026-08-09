package com.nusatim.partner.core.domain.repository

import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import com.nusatim.partner.core.model.dto.ProjectResponse
import kotlinx.coroutines.flow.Flow

interface ProjectsRepository {
    fun getProjects(
        page: Int? = null,
        perPage: Int? = null
    ): Flow<ResultState<PagedBaseResponse<ProjectResponse>>>

    fun getProjectDetail(id: Int): Flow<ResultState<ProjectResponse>>

    fun claimProject(id: Int): Flow<ResultState<ProjectResponse>>

    fun cancelClaimProject(id: Int): Flow<ResultState<ProjectResponse>>
}
