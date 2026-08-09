package com.nusatim.partner.features.projects.ui

import androidx.fragment.app.testing.launchFragmentInContainer
import androidx.test.ext.junit.runners.AndroidJUnit4
import com.nusatim.partner.core.model.ResultState
import com.nusatim.partner.core.model.dto.PagedBaseResponse
import com.nusatim.partner.core.model.dto.ProjectResponse
import com.nusatim.partner.features.projects.domain.usecase.GetProjectsUseCase
import com.nusatim.partner.features.projects.robot.projects
import com.nusatim.partner.features.projects.ui.board.ProjectsFragment
import io.mockk.coEvery
import io.mockk.mockk
import kotlinx.coroutines.flow.flowOf
import org.junit.Test
import org.junit.runner.RunWith

@RunWith(AndroidJUnit4::class)
class ProjectsFragmentTest {

    private val getProjectsUseCase: GetProjectsUseCase = mockk()

    @Test
    fun displayProjectList() {
        val projectsResponse = PagedBaseResponse(
            data = listOf(
                ProjectResponse(
                    id = 1,
                    name = "Project A",
                    description = "Desc",
                    serviceId = 1,
                    serviceName = "Service",
                    budget = 1000,
                    location = "Jakarta",
                    deadline = "2024-12-31",
                    difficulty = "medium",
                    commissionValue = 100,
                    status = "available",
                    progress = null,
                    isMine = false,
                    claimedAt = null
                )
            ),
            links = mockk(),
            meta = mockk()
        )
        
        coEvery { getProjectsUseCase(any(), any()) } returns flowOf(ResultState.Success(projectsResponse))

        launchFragmentInContainer<ProjectsFragment>()

        projects {
            verifyProjectListIsDisplayed()
        }
    }
}
