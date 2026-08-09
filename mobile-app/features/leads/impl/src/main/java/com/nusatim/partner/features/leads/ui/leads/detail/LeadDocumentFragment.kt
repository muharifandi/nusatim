package com.nusatim.partner.features.leads.ui.leads.detail

import androidx.fragment.app.viewModels
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import androidx.recyclerview.widget.LinearLayoutManager
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.features.leads.databinding.FragmentLeadDocumentBinding
import com.nusatim.partner.features.leads.ui.leads.detail.state.LeadDetailIntent
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.launch

@AndroidEntryPoint
class LeadDocumentFragment : BaseFragment<FragmentLeadDocumentBinding>() {

    private val viewModel: LeadDetailViewModel by viewModels({ requireParentFragment() })
    private val adapter by lazy {
        LeadDocumentAdapter { document ->
            // TODO: Open/Download document
        }
    }

    override fun onInitViews() {
        binding.rvDocuments.layoutManager = LinearLayoutManager(requireContext())
        binding.rvDocuments.adapter = adapter
        
        binding.fabUpload.setOnClickListener {
            // TODO: Pick file and upload
        }
        
        val leadId = viewModel.state.value.lead?.id
        if (leadId != null) {
            viewModel.processIntent(LeadDetailIntent.LoadDocuments(leadId))
        }
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                viewModel.state.collect { state ->
                    adapter.submitList(state.documents)

                    val isEmpty = state.documents.isEmpty() && !state.isLoading
                    binding.layoutEmpty.root.visibility = if (isEmpty) android.view.View.VISIBLE else android.view.View.GONE
                    if (isEmpty) {
                        binding.layoutEmpty.tvErrorMessage.text = getString(com.nusatim.partner.features.leads.R.string.lead_tab_document_empty)
                        binding.layoutEmpty.btnRetry.visibility = android.view.View.GONE
                    }
                }
            }
        }
    }
}
