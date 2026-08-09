package com.nusatim.partner.features.leads.ui.pipeline

import androidx.core.net.toUri
import androidx.fragment.app.viewModels
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import androidx.navigation.fragment.findNavController
import androidx.recyclerview.widget.LinearLayoutManager
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.core.model.dto.PipelineResponse
import com.nusatim.partner.features.leads.databinding.FragmentPipelineBinding
import com.nusatim.partner.features.leads.ui.leads.detail.ChangeStatusBottomSheet
import com.nusatim.partner.features.leads.ui.pipeline.state.PipelineIntent
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.launch

@AndroidEntryPoint
class PipelineFragment : BaseFragment<FragmentPipelineBinding>() {

    private val viewModel: PipelineViewModel by viewModels()
    private val boardAdapter by lazy {
        PipelineBoardAdapter(
            onLeadClick = { lead ->
                findNavController().navigate("partner://leads/detail?id=${lead.id}".toUri())
            },
            onLeadLongClick = { lead ->
                showMoveStatusBottomSheet(lead)
            }
        )
    }

    override fun onInitViews() {
        binding.rvPipelineBoard.layoutManager = LinearLayoutManager(requireContext(), LinearLayoutManager.VERTICAL, false)
        binding.rvPipelineBoard.adapter = boardAdapter

        binding.swipeRefresh.setOnRefreshListener {
            viewModel.processIntent(PipelineIntent.LoadPipeline)
        }

        binding.layoutError.btnRetry.setOnClickListener {
            viewModel.processIntent(PipelineIntent.LoadPipeline)
        }
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                viewModel.state.collect { state ->
                    binding.swipeRefresh.isRefreshing = state.isLoading
                    state.pipeline?.let { 
                        mapToColumns(it)
                        binding.layoutError.root.visibility = android.view.View.GONE
                    }
                    
                    if (state.error != null && state.pipeline == null) {
                        binding.layoutError.root.visibility = android.view.View.VISIBLE
                        binding.layoutError.tvErrorMessage.text = state.error
                    }
                }
            }
        }
    }

    private fun mapToColumns(pipeline: PipelineResponse) {
        val columns = listOf(
            PipelineColumn("New", "new", pipeline.newLeads),
            PipelineColumn("Contacted", "contacted", pipeline.contactedLeads),
            PipelineColumn("Qualified", "qualified", pipeline.qualifiedLeads),
            PipelineColumn("Opportunity", "opportunity", pipeline.opportunityLeads),
            PipelineColumn("Proposal", "proposal", pipeline.proposalLeads),
            PipelineColumn("Negotiation", "negotiation", pipeline.negotiationLeads),
            PipelineColumn("Won", "won", pipeline.wonLeads),
            PipelineColumn("Lost", "lost", pipeline.lostLeads)
        )
        boardAdapter.submitColumns(columns)
    }

    private fun showMoveStatusBottomSheet(lead: com.nusatim.partner.core.model.dto.LeadResponse) {
        ChangeStatusBottomSheet(lead.status ?: "new") { newStatus ->
            viewModel.processIntent(PipelineIntent.UpdateStatus(lead.id, newStatus))
        }.show(childFragmentManager, ChangeStatusBottomSheet.TAG)
    }
}
