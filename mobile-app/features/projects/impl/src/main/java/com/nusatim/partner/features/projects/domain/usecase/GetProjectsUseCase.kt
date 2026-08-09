package com.nusatim.partner.features.projects.domain.usecase

import com.nusatim.partner.core.domain.repository.ProjectsRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import com.nusatim.partner.core.model.dto.ProjectResponse
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class GetProjectsUseCase @Inject constructor(
    private val repository: ProjectsRepository
) {
    operator fun invoke(page: Int? = null, perPage: Int? = null): Flow<ResultState<PagedBaseResponse<ProjectResponse>>> {
        return repository.getProjects(page, perPage)
    }
}
