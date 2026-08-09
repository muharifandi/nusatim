package com.nusatim.partner.core.data.repository

import com.nusatim.partner.core.domain.repository.ProjectsRepository
import com.nusatim.partner.core.data.repository.BaseRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.network.ProjectsApiService
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import com.nusatim.partner.core.model.dto.ProjectResponse
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class ProjectsRepositoryImpl @Inject constructor(
    private val apiService: ProjectsApiService
) : BaseRepository(), ProjectsRepository {

    override fun getProjects(
        page: Int?,
        perPage: Int?
    ): Flow<ResultState<PagedBaseResponse<ProjectResponse>>> = safeNetworkCall {
        apiService.getProjects(page, perPage)
    }

    override fun getProjectDetail(id: Int): Flow<ResultState<ProjectResponse>> = safeNetworkCall {
        apiService.getProjectDetail(id).data
    }

    override fun claimProject(id: Int): Flow<ResultState<ProjectResponse>> = safeNetworkCall {
        apiService.claimProject(id).data
    }

    override fun cancelClaimProject(id: Int): Flow<ResultState<ProjectResponse>> = safeNetworkCall {
        apiService.cancelClaimProject(id).data
    }
}
