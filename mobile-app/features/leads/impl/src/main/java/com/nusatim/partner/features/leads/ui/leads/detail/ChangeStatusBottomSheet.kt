package com.nusatim.partner.features.leads.ui.leads.detail

import android.os.Bundle
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import com.google.android.material.bottomsheet.BottomSheetDialogFragment
import com.nusatim.partner.features.leads.R
import com.nusatim.partner.features.leads.databinding.BottomSheetChangeStatusBinding

class ChangeStatusBottomSheet(
    private val currentStatus: String,
    private val onStatusSelected: (String) -> Unit
) : BottomSheetDialogFragment() {

    private var _binding: BottomSheetChangeStatusBinding? = null
    private val binding get() = _binding!!

    override fun onCreateView(inflater: LayoutInflater, container: ViewGroup?, savedInstanceState: Bundle?): View {
        _binding = BottomSheetChangeStatusBinding.inflate(inflater, container, false)
        return binding.root
    }

    override fun onViewCreated(view: View, savedInstanceState: Bundle?) {
        super.onViewCreated(view, savedInstanceState)
        
        setupInitialSelection()

        binding.btnConfirm.setOnClickListener {
            val selectedStatus = when (binding.rgStatus.checkedRadioButtonId) {
                R.id.rb_new -> "new"
                R.id.rb_contacted -> "contacted"
                R.id.rb_qualified -> "qualified"
                R.id.rb_opportunity -> "opportunity"
                R.id.rb_proposal -> "proposal"
                R.id.rb_negotiation -> "negotiation"
                R.id.rb_won -> "won"
                R.id.rb_lost -> "lost"
                else -> currentStatus
            }
            onStatusSelected(selectedStatus)
            dismiss()
        }
    }

    private fun setupInitialSelection() {
        val checkedId = when (currentStatus.lowercase()) {
            "new" -> R.id.rb_new
            "contacted" -> R.id.rb_contacted
            "qualified" -> R.id.rb_qualified
            "opportunity" -> R.id.rb_opportunity
            "proposal" -> R.id.rb_proposal
            "negotiation" -> R.id.rb_negotiation
            "won" -> R.id.rb_won
            "lost" -> R.id.rb_lost
            else -> -1
        }
        if (checkedId != -1) {
            binding.rgStatus.check(checkedId)
        }
    }

    override fun onDestroyView() {
        super.onDestroyView()
        _binding = null
    }

    companion object {
        const val TAG = "ChangeStatusBottomSheet"
    }
}
