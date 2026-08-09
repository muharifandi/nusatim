package com.nusatim.partner.features.projects.domain.usecase

import com.nusatim.partner.core.domain.repository.ProjectsRepository
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.ProjectResponse
import kotlinx.coroutines.flow.Flow
import javax.inject.Inject

class GetProjectDetailUseCase @Inject constructor(
    private val repository: ProjectsRepository
) {
    operator fun invoke(id: Int): Flow<ResultState<ProjectResponse>> {
        return repository.getProjectDetail(id)
    }
}
