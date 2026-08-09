package com.nusatim.partner.features.projects.robot

import com.nusatim.partner.core.testing.robot.BaseRobot
import com.nusatim.partner.features.projects.R

class ProjectsRobot : BaseRobot() {
    fun verifyProjectListIsDisplayed() = viewIsDisplayed(R.id.rv_projects)
    fun clickProjectItem(position: Int) = clickRecyclerViewItem(R.id.rv_projects, position)
}

fun projects(func: ProjectsRobot.() -> Unit) = ProjectsRobot().apply { func() }
